<?php
namespace HealthCareAbroad\AdminBundle\Controller;

use Symfony\Component\Form\FormError;

use Symfony\Component\HttpFoundation\Request;

use HealthCareAbroad\AdminBundle\Event\AdminBundleEvents;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use HealthCareAbroad\HelperBundle\Entity\Country;
use HealthCareAbroad\HelperBundle\Form\CountryFormType;
use JMS\SecurityExtraBundle\Annotation\PreAuthorize;

class CountryController extends Controller
{
    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_VIEW_COUNTRIES')")
     */
    public function indexAction()
    {
        return $this->render('AdminBundle:Country:index.html.twig', array(
            'countries' => $this->filteredResult,
            'statuses' => array(Country::STATUS_ACTIVE => 'Active', Country::STATUS_INACTIVE => 'Inactive', Country::STATUS_NEW => 'New'),
            'pager' => $this->pager
        ));
    }

    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_COUNTRY')")
     */
    public function addAction()
    {
        $form = $this->createForm(New CountryFormType());

        return $this->render('AdminBundle:Country:form.html.twig', array(
            'id' => null,
            'form' => $form->createView(),
            'formAction' => $this->generateUrl('admin_country_create')
        ));
    }

    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_COUNTRY')")
     */
    public function editAction($id)
    {
        $country = $this->get('services.location')->getGlobalCountryById($id);
        
        $form = $this->createForm(New CountryFormType(), $country);
        
        return $this->render('AdminBundle:Country:form.html.twig', array(
            'id' => $id,
            'form' => $form->createView(),
            'formAction' => $this->generateUrl('admin_country_update', array('id' => $id))
        ));
       
    }

    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_COUNTRY')")
     */
    public function saveAction(Request $request)
    {        
        $formData = $request->get(CountryFormType::NAME);
        $form = $this->createForm(new CountryFormType(), $formData);
        
        if($id = $request->get('id', null)) {
            $formAction = $this->generateUrl('admin_country_update', array('id' => $id));
            $result = $this->get('services.location')->updateGlobalCountry($formData, $id);
        } else {
            $formAction = $this->generateUrl('admin_country_create'); 
            $result = $this->get('services.location')->addGlobalCountry($formData);
        }
        
        // if no errors
        if(!isset($result['form'])) {
            if($id && ($countryObj = $this->get('services.location')->updateCountry($result, $id))) {
                $eventName = AdminBundleEvents::ON_EDIT_COUNTRY;
                $this->get('event_dispatcher')->dispatch($eventName, $this->get('events.factory')->create($eventName, $countryObj));
            }
        
            $request->getSession()->setFlash('success', 'Country has been saved!');
            $routeParams = array(
                'status' => $formData['status'],
            );
        
            return $this->redirect($this->generateUrl('admin_country_index', $routeParams));
        }
        
        // Bind API Form Errors to client form
        foreach($result['form']['children'] as $property => $data) {
            if(isset($data['errors'])) {
                $form->get($property)->addError(new FormError($data['errors'][0]));
            }
        }
        
        return $this->render('AdminBundle:Country:form.html.twig', array(
            'id' => $id,
            'form' => $form->createView(),
            'formAction' => $formAction
        ));
    }

    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_COUNTRY')")
     */
    public function updateStatusAction(Request $request)
    {
        $country = $this->get('services.location')->updateGlobalCountryStatus($request->get('id'), $request->get('status'));
        
        $em = $this->getDoctrine()->getEntityManager();
        $countryObj = $em->getRepository('HelperBundle:Country')->find($country['id']);
        
        if ($countryObj) {
            $countryObj->setStatus($country['status']);
            $em->persist($countryObj);
            $em->flush($countryObj);

            // dispatch event
            $this->get('event_dispatcher')->dispatch(AdminBundleEvents::ON_EDIT_COUNTRY, $this->get('events.factory')->create(AdminBundleEvents::ON_EDIT_COUNTRY, $countryObj));
        }

        $response = new Response(json_encode($country));
        $response->headers->set('Content-Type', 'application/json');
        
        return $response;
    }
}