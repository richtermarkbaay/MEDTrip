<?php
/**
 * @author adelbertsilla
 */

namespace HealthCareAbroad\AdminBundle\Controller;

use Doctrine\Common\Util\Inflector;

use HealthCareAbroad\HelperBundle\Services\LocationService;

use Symfony\Component\Form\FormError;

use HealthCareAbroad\HelperBundle\Form\FieldType\CityNameFieldType;

use Symfony\Component\HttpFoundation\Request;

use HealthCareAbroad\AdminBundle\Event\AdminBundleEvents;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use HealthCareAbroad\HelperBundle\Entity\City;
use HealthCareAbroad\HelperBundle\Form\CityFormType;
use JMS\SecurityExtraBundle\Annotation\PreAuthorize;

class CityController extends Controller
{
    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_VIEW_CITIES')")
     *
     */
    public function indexAction()
    {
        return $this->render('AdminBundle:City:index.html.twig', array(
            'statusNew' => City::STATUS_NEW,
            'statuses' => array(City::STATUS_NEW => 'New', City::STATUS_ACTIVE => 'Active', City::STATUS_INACTIVE => 'Inactive'),
            'cities' => $this->filteredResult,
            'pager' => $this->pager
        ));
    }

    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_CITY')")
     */
    public function addAction()
    {
        $form = $this->createForm(New CityFormType());

        return $this->render('AdminBundle:City:form.html.twig', array(
            'id' => null,
            'form' => $form->createView(),
            'formAction' => $this->generateUrl('admin_city_create')
        ));
    }

    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_CITY')")
     */
    public function editAction($id)
    {
        $city = $this->get('services.location')->getGlobalCityById($id); 
        if(isset($city['geoCountry'])) {
            $city['geoCountry'] = $city['geoCountry']['id'];
        }
        if(isset($city['geoState'])) {
            $city['geoState'] = $city['geoState']['id'];
        }

        $form = $this->createForm(New CityFormType(), $city);

        return $this->render('AdminBundle:City:form.html.twig', array(
            'id' => $id,
            'form' => $form->createView(),
            'formAction' => $this->generateUrl('admin_city_update', array('id' => $id))
        ));
    }

    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_CITY')")
     */
    public function saveAction(Request $request)
    {
        if('POST' != $request->getMethod()) {
            return new Response("Save requires POST method!", 405);
        }

        $formData = $request->get(CityFormType::NAME);
        $form = $this->createForm(new CityFormType(), $formData);

        if($id = $request->get('id', null)) {
            $formAction = $this->generateUrl('admin_city_update', array('id' => $id));
            $result = $this->get('services.location')->updateGlobalCity($formData, $id);
        } else {
            $formAction = $this->generateUrl('admin_city_create');
            $result = $this->get('services.location')->addGlobalCity($formData);
        }

        // if no errors
        if(!isset($result['form'])) {
            if($id) {
                $this->get('services.location')->updateCity($result, $id);
            }

            $request->getSession()->setFlash('success', 'City has been saved!');
            $routeParams = array(
                'country' => $formData['geoCountry'],
                'state' => isset($formData['geoState']) ? $formData['geoState'] : 0, 
                'status' => $formData['status'],
            );

            return $this->redirect($this->generateUrl('admin_city_index', $routeParams));
        }

        // Bind API Form Errors to client form 
        foreach($result['form']['children'] as $property => $data) {
            if(isset($data['errors'])) {
                $form->get($property)->addError(new FormError($data['errors'][0]));                
            }
        }

        return $this->render('AdminBundle:City:form.html.twig', array(
            'id' => $id,
            'form' => $form->createView(),
            'formAction' => $formAction
        ));
    }

    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_CITY')")
     */
    public function updateStatusAction(Request $request)
    {
        $city = $this->get('services.location')->updateGlobalCityStatus($request->get('id'), $request->get('status'));

        $em = $this->getDoctrine()->getEntityManager();
        $cityObj = $em->getRepository('HelperBundle:City')->find($city['id']);

        if ($cityObj) {
            $cityObj->setStatus($city['status']);
            $cityObj->setInstitutonId($city['institutionId']);
            $em->persist($cityObj);
            $em->flush($cityObj);

            // dispatch event
            $this->get('event_dispatcher')->dispatch(AdminBundleEvents::ON_EDIT_CITY, $this->get('events.factory')->create(AdminBundleEvents::ON_EDIT_CITY, $city));
        }

        $response = new Response(json_encode($city));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}