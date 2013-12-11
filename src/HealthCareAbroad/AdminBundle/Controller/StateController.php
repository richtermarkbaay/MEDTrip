<?php
/**
 * @author adelbertsilla
 */

namespace HealthCareAbroad\AdminBundle\Controller;

use Doctrine\Common\Util\Inflector;

use HealthCareAbroad\HelperBundle\Services\LocationService;

use Symfony\Component\Form\FormError;

use Symfony\Component\HttpFoundation\Request;

use HealthCareAbroad\AdminBundle\Event\AdminBundleEvents;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use HealthCareAbroad\HelperBundle\Entity\State;
use HealthCareAbroad\HelperBundle\Form\StateFormType;
use JMS\SecurityExtraBundle\Annotation\PreAuthorize;

class StateController extends Controller
{
    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_VIEW_STATES')")
     *
     */
    public function indexAction()
    {
        return $this->render('AdminBundle:State:index.html.twig', array(
            'statusNew' => State::STATUS_NEW,
            'statuses' => array(State::STATUS_NEW => 'New', State::STATUS_ACTIVE => 'Active', State::STATUS_INACTIVE => 'Inactive'),
            'states' => $this->filteredResult,
            'pager' => $this->pager
        ));
    }

    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_STATE')")
     */
    public function addAction()
    {
        $form = $this->createForm(New StateFormType());

        return $this->render('AdminBundle:State:form.html.twig', array(
            'id' => null,
            'form' => $form->createView(),
            'formAction' => $this->generateUrl('admin_state_create')
        ));
    }

    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_STATE')")
     */
    public function editAction($id)
    {
        $state = $this->get('services.location')->getGlobalStateById($id); 
        if(isset($state['geoCountry'])) {
            $state['geoCountry'] = $state['geoCountry']['id'];
        }

        $form = $this->createForm(New StateFormType(), $state);

        return $this->render('AdminBundle:State:form.html.twig', array(
            'id' => $id,
            'form' => $form->createView(),
            'formAction' => $this->generateUrl('admin_state_update', array('id' => $id))
        ));
    }

    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_STATE')")
     */
    public function saveAction(Request $request)
    {
        if('POST' != $request->getMethod()) {
            return new Response("Save requires POST method!", 405);
        }

        $formData = $request->get(StateFormType::NAME);
        $form = $this->createForm(new StateFormType(), $formData);

        if($id = $request->get('id', null)) {
            $formAction = $this->generateUrl('admin_state_update', array('id' => $id));
            $result = $this->get('services.location')->updateGlobalState($formData, $id);
        } else {
            $formAction = $this->generateUrl('admin_state_create');
            $result = $this->get('services.location')->addGlobalState($formData);
        }

        // if no errors
        if(!isset($result['form'])) {
            if($id) {
                $this->get('services.location')->updateState($result, $id);
            }

            $request->getSession()->setFlash('success', 'State has been saved!');
            $routeParams = array(
                'country' => $formData['geoCountry'], 
                'status' => $formData['status'],
            );

            return $this->redirect($this->generateUrl('admin_state_index', $routeParams));
        }

        // Bind API Form Errors to client form 
        foreach($result['form']['children'] as $property => $data) {
            if(isset($data['errors'])) {
                $form->get($property)->addError(new FormError($data['errors'][0]));                
            }
        }

        return $this->render('AdminBundle:State:form.html.twig', array(
            'id' => $id,
            'form' => $form->createView(),
            'formAction' => $formAction
        ));
    }

    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_STATE')")
     */
    public function updateStatusAction(Request $request)
    {
        $state = $this->get('services.location')->updateGlobalStateStatus($request->get('id'), $request->get('status'));

        $em = $this->getDoctrine()->getEntityManager();
        $stateObj = $em->getRepository('HelperBundle:State')->find($state['id']);

        if ($stateObj) {
            $stateObj->setStatus($state['status']);
            $stateObj->setInstitutionId($state['institutionId']);
            $em->persist($stateObj);
            $em->flush($stateObj);

            // dispatch event
            $this->get('event_dispatcher')->dispatch(AdminBundleEvents::ON_EDIT_STATE, $this->get('events.factory')->create(AdminBundleEvents::ON_EDIT_STATE, $stateObj));
        }

        $response = new Response(json_encode($state));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}