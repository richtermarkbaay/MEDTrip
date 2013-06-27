<?php
namespace HealthCareAbroad\AdminBundle\Controller;

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
        echo $this->get('page');exit;
        return $this->render('AdminBundle:Country:index.html.twig', array(
            'countries' => $this->filteredResult,
            'pager' => $this->pager
        ));
    }

    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_COUNTRY')")
     */
    public function addAction()
    {
        $form = $this->createForm(New CountryFormType(), new Country());

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
        $locationService = $this->get('services.location'); 
        $country = $locationService->getGlobalCountryById($id);
        $country = $locationService->createCountryFromArray($country);
        
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
    public function saveAction()
    {
        $request = $this->getRequest();
        if('POST' != $request->getMethod()) {
            return new Response("Save requires POST method!", 405);
        }

        $locationService = $this->get('services.location');
        
        if($id = $request->get('id', null)) {
            $country = $locationService->getGlobalCountryById($id);
            $country = $locationService->createCountryFromArray($country);
        } else {
            $country = new Country();
        }
        
        $form = $this->createForm(New CountryFormType(), $country);
        $form->bind($request);
        if ($form->isValid()) {
            if(!$id) {
                $data = $request->get('country');
            }
            else {
                $data = array(
                        'id' => $country->getId(),
                        'name' => $country->getName(),
                        'abbr' => $country->getAbbr(),
                        'code' =>  $country->getCode(),
                        'status' => $country->getStatus()
                );
            }
            
            $country = $locationService->saveGlobalCountry($data);
            
            // dispatch event
            $eventName = $id ? AdminBundleEvents::ON_EDIT_COUNTRY : AdminBundleEvents::ON_ADD_COUNTRY;
            // $eventName;exit;
            $this->get('event_dispatcher')->dispatch($eventName, $this->get('events.factory')->create($eventName, $country));
            
            $request->getSession()->setFlash('success', 'Country has been saved!');
            
            return $this->redirect($this->generateUrl('admin_country_index'));
        }
        $formAction = $id ? $this->generateUrl('admin_country_update', array('id' => $id)) : $this->generateUrl('admin_country_create');

        return $this->render('AdminBundle:Country:form.html.twig', array(
                'id' => $id,
                'form' => $form->createView(),
                'formAction' => $formAction
        ));
    }

    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_DELETE_COUNTRY')")
     */
    public function updateStatusAction($id)
    {
        $country = $this->get('services.location')->updateStatusGlobalCountry($id);
        
        if ($country) {
            
            // dispatch event
            $this->get('event_dispatcher')->dispatch(AdminBundleEvents::ON_EDIT_COUNTRY, $this->get('events.factory')->create(AdminBundleEvents::ON_EDIT_COUNTRY, $country));

            $result = true;
        }

        $response = new Response(json_encode($result));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}