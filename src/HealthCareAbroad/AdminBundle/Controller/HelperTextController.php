<?php

/*
 * @author Chaztine Blance
 * Add Helper Data
 */
namespace HealthCareAbroad\AdminBundle\Controller;

use HealthCareAbroad\HelperBundle\Entity\RouteType;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use HealthCareAbroad\AdminBundle\Event\AdminBundleEvents;
use Symfony\Component\HttpFoundation\Response;
use HealthCareAbroad\HelperBundle\Entity\HelperText;
use HealthCareAbroad\HelperBundle\Form\HelperTextFormType;
use JMS\SecurityExtraBundle\Annotation\PreAuthorize;

class HelperTextController extends Controller
{
    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_VIEW_HELPER_TEXT')")
     */
    public function indexAction()
    {
        $helperData = $this->getDoctrine()->getRepository('HelperBundle:HelperText')->findAll();
        
        return $this->render('AdminBundle:Helper:index.html.twig', array(
                        'helperText' => $helperData,
                        'routeLabel' => RouteType::getFormChoicesLabel()
        ));
    }
    
    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_HELPER_TEXT')")
     */
//     public function addAction()
//     {
 
//         $helperData = $this->getDoctrine()->getEntityManager()
//         ->getRepository('HelperBundle:HelperText')->find($id);
        
//         $form = $this->createForm(New HelperTextFormType(), $helperData);
    
//         return $this->render('AdminBundle:Helper:form.html.twig', array(
//                         'id' => null,
//                         'form' => $form->createView(),
//                         'formAction' => $this->generateUrl('admin_helper_text_create'),
//                         'newObject' => true
//         ));
//     }
    
    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_HELPER_TEXT')")
     */
    public function editAction($id)
    {
        $helperData = $this->getDoctrine()->getEntityManager()
        ->getRepository('HelperBundle:HelperText')->find($id);
    
        $form = $this->createForm(New HelperTextFormType(), $helperData);
    
        return $this->render('AdminBundle:Helper:form.html.twig', array(
                        'id' => $id,
                        'form' => $form->createView(),
                        'formAction' => $this->generateUrl('admin_helper_text_update', array('id' => $id)),
        ));
    }
    
    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_HELPER_TEXT')")
     */
    public function saveAction()
    {
        $request = $this->getRequest();
        if('POST' != $request->getMethod()) {
            return new Response("Save requires POST method!", 405);
        }
    
        $id = $request->get('id', null);
        $em = $this->getDoctrine()->getEntityManager();
    
        $helperData = $id ? $em->getRepository('HelperBundle:HelperText')->find($id) : new HelperText();
    
        $form = $this->createForm(New HelperTextFormType(), $helperData);
        $form->bind($request);
    
        if ($form->isValid()) {
            $em->persist($helperData);
            $em->flush($helperData);
    
            // dispatch event
            $eventName = $id ? AdminBundleEvents::ON_EDIT_HELPER_TEXT : AdminBundleEvents::ON_ADD_HELPER_TEXT;
            $this->get('event_dispatcher')->dispatch($eventName, $this->get('events.factory')->create($eventName, $helperData));
             
            $request->getSession()->setFlash('success', 'Data has been saved!');
    
            return $this->redirect($this->generateUrl('admin_helper_text_index'));
        }
    
        $formAction = $id ? $this->generateUrl('admin_helper_text_update', array('id' => $id)) : $this->generateUrl('admin_helper_text_create');
    
        return $this->render('AdminBundle:Helper:form.html.twig', array(
                        'id' => $id,
                        'form' => $form->createView(),
                        'formAction' => $formAction
        ));
    }
    
    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_DELETE_HELPER_TEXT')")
     */
    public function updateStatusAction($id)
    {
        $result = false;
        $em = $this->getDoctrine()->getEntityManager();
        $helperData = $em->getRepository('HelperBundle:Helper')->find($id);
    
        if ($helperData) {
            $helperData->setStatus($helperData->getStatus() ? $helperData::STATUS_INACTIVE : $helperData::STATUS_ACTIVE);
            $em->persist($helperData);
            $em->flush($helperData);
    
            // dispatch event
            $this->get('event_dispatcher')->dispatch(AdminBundleEvents::ON_EDIT_HELPER_TEXT, $this->get('events.factory')->create(AdminBundleEvents::ON_EDIT_HELPER_TEXT, $helperData));
    
            $result = true;
        }
    
        $response = new Response(json_encode($result));
        $response->headers->set('Content-Type', 'application/json');
    
        return $response;
    }
}