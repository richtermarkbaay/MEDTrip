<?php

namespace HealthCareAbroad\AdminBundle\Controller;

use HealthCareAbroad\AdminBundle\Event\AdminBundleEvents;

use HealthCareAbroad\HelperBundle\Services\Filters\ListFilter;

use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use HealthCareAbroad\TreatmentBundle\Entity\SubSpecialization;
use HealthCareAbroad\TreatmentBundle\Form\SubSpecializationFormType;
use JMS\SecurityExtraBundle\Annotation\PreAuthorize;

class SubSpecializationController extends Controller
{

    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_VIEW_SUB_SPECIALIZATIONS')")
     */
    public function indexAction(Request $request)
    {
        $specializationId = $request->get('specialization', 0);
        if ($specializationId == ListFilter::FILTER_KEY_ALL) {
            $specializationId = 0;
        }

        $params = array('specializationId' => $specializationId,'subSpecializations'=> $this->filteredResult, 'pager' => $this->pager);

        return $this->render('AdminBundle:SubSpecialization:index.html.twig', $params);
    }

    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_SUB_SPECIALIZATION')")
     */
    public function addAction()
    {
        $params = $formActionParams = array();
        $subSpecialization = new SubSpecialization();

        $specializationId = (int)$this->getRequest()->get('specializationId');

        if($specializationId) {
            $specialization = $this->getDoctrine()->getRepository('TreatmentBundle:Specialization')->find($specializationId);

            if(!$specialization) {
                throw $this->createNotFoundException("Invalid Specialization.");
            }

            $subSpecialization->setSpecialization($specialization);
            $formActionParams['specializationId'] = $specializationId;
        }

        $subSpecializationForm = new SubSpecializationFormType();
        $subSpecializationForm->setDoctrine($this->getDoctrine());

        $form = $this->createForm($subSpecializationForm, $subSpecialization);

        $params['form'] = $form->createView();
        $params['formAction'] = $this->generateUrl('admin_subSpecialization_create', $formActionParams);

        return $this->render('AdminBundle:SubSpecialization:form.html.twig', $params);
    }
    
    /**
     * 
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_SUB_SPECIALIZATION')")
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction($id)
    {
        $subSpecialization = $this->getDoctrine()->getRepository('TreatmentBundle:SubSpecialization')->find($id);

        if(!$subSpecialization) {
            throw $this->createNotFoundException("Invalid SubSpecialization.");
        }

        $subSpecializationForm = new SubSpecializationFormType();
        $subSpecializationForm->setDoctrine($this->getDoctrine());

        $form = $this->createForm($subSpecializationForm, $subSpecialization);

        $params = array(
            'form' => $form->createView(),
            'formAction' =>  $this->generateUrl('admin_subSpecialization_update', array('id' => $subSpecialization->getId())),
            'hasTreatment' => (bool)count($subSpecialization->getTreatments()),
            'subSpecialization' => $subSpecialization
        );

        return $this->render('AdminBundle:SubSpecialization:form.html.twig', $params);
    }

    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_SUB_SPECIALIZATION')")
     */
    public function saveAction()
    {
        $request = $this->getRequest();
        if('POST' != $request->getMethod()) {
            return new Response("Save requires POST method!", 405);
        }

        $id = $request->get('id', null);
        $em = $this->getDoctrine()->getEntityManager();

        if($id) {
            $subSpecialization = $em->getRepository('TreatmentBundle:SubSpecialization')->find($id);
            if(!$subSpecialization) {
                throw $this->createNotFoundException("Invalid SubSpecialization.");
            }
        } 
        else {
            $subSpecialization = new SubSpecialization();
        }

        $subSpecializationForm = new SubSpecializationFormType();
        $subSpecializationForm->setDoctrine($this->getDoctrine());

        $form = $this->createForm($subSpecializationForm, $subSpecialization);
        $form->bind($request);

        if ($form->isValid()) {
            $em->persist($subSpecialization);
            $em->flush($subSpecialization);
    
            $eventName = $id ? AdminBundleEvents::ON_EDIT_SUB_SPECIALIZATION : AdminBundleEvents::ON_ADD_SUB_SPECIALIZATION;
            $this->get('event_dispatcher')->dispatch($eventName, $this->get('events.factory')->create($eventName, $subSpecialization));
            
            $request->getSession()->setFlash('success', $id ? "Successfully updated {$subSpecialization->getName()}." : "Successfully added {$subSpecialization->getName()}.");

            if($request->get('submit') == 'Save')
                return $this->redirect($this->generateUrl('admin_subSpecialization_edit', array('id' => $subSpecialization->getId())));
            else {
                $specializationId = $request->get('specializationId');
                $addParams = $specializationId ? array('specializationId' => $specializationId) : array();

                return $this->redirect($this->generateUrl('admin_subSpecialization_add', $addParams));
            }

        } else {

            if(!$subSpecialization->getId()) {
                $specializationId = $request->get('specializationId');
                $formAction = $this->generateUrl('admin_subSpecialization_create', $specializationId ? array('specializationId' => $specializationId) : array());
            } else {
                $formAction = $this->generateUrl('admin_subSpecialization_update', array('id' => $subSpecialization->getId()));
            }

            $params = array(
                'form' => $form->createView(),
                'formAction' => $formAction,
                'hasProcedures' => (bool)count($subSpecialization->getTreatments())
            );
            return $this->render('AdminBundle:SubSpecialization:form.html.twig', $params);
        }
    }
    
    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_DELETE_SUB_SPECIALIZATION')")
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function updateStatusAction($id)
    {
        $result = false;
        $subSpecialization = $this->get('services.treatmentBundle')->getSubSpecialization($id);

        if($subSpecialization) {
            $em = $this->getDoctrine()->getEntityManager();
            
            $status = $subSpecialization->getStatus() == SubSpecialization::STATUS_ACTIVE
                    ? SubSpecialization::STATUS_INACTIVE 
                    : SubSpecialization::STATUS_ACTIVE;

            $subSpecialization->setStatus($status);
            $em->persist($subSpecialization);
            $em->flush($subSpecialization);
            
            // dispatch event
            $this->get('event_dispatcher')->dispatch(AdminBundleEvents::ON_EDIT_SUB_SPECIALIZATION, $this->get('events.factory')->create(AdminBundleEvents::ON_EDIT_SUB_SPECIALIZATION, $subSpecialization));
            
            $result = true;
        }

        $response = new Response(json_encode($result));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}