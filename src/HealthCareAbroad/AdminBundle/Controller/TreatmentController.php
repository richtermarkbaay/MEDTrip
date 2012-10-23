<?php

namespace HealthCareAbroad\AdminBundle\Controller;

use HealthCareAbroad\AdminBundle\Event\AdminBundleEvents;

use HealthCareAbroad\HelperBundle\Services\Filters\ListFilter;

use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use HealthCareAbroad\MedicalProcedureBundle\Entity\Treatment;
use HealthCareAbroad\MedicalProcedureBundle\Form\TreatmentFormType;
use JMS\SecurityExtraBundle\Annotation\PreAuthorize;

class TreatmentController extends Controller
{

    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_VIEW_PROCEDURE_TYPES')")
     */
    public function indexAction(Request $request)
    {
        $specializationId = $request->get('specialization', 0);
        if ($specializationId == ListFilter::FILTER_KEY_ALL) {
            $specializationId = 0;
        }

        $params = array('specializationId' => $specializationId,'treatments'=> $this->filteredResult, 'pager' => $this->pager);
        
        return $this->render('AdminBundle:Treatment:index.html.twig', $params);
    }

    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_PROCEDURE_TYPE')")
     */
    public function addAction()
    {
        $params = $formActionParams = array();
        $treatment = new Treatment();

        $specializationId = (int)$this->getRequest()->get('specializationId');

        if($specializationId) {
            $specialization = $this->getDoctrine()->getRepository('MedicalProcedureBundle:MedicalCenter')->find($specializationId);

            if(!$specialization) {
                throw $this->createNotFoundException("Invalid Specialization.");
            }

            $treatment->setMedicalCenter($specialization);
            $formActionParams['specializationId'] = $specializationId;
        }

        $treatmentForm = new TreatmentFormType();
        $treatmentForm->setDoctrine($this->getDoctrine());

        $form = $this->createForm($treatmentForm, $treatment);

        $params['form'] = $form->createView();
        $params['formAction'] = $this->generateUrl('admin_treatment_create', $formActionParams);

        return $this->render('AdminBundle:Treatment:form.html.twig', $params);
    }
    
    /**
     * 
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_PROCEDURE_TYPE')")
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction($id)
    {
        $treatment = $this->getDoctrine()->getRepository('MedicalProcedureBundle:Treatment')->find($id);

        if(!$treatment) {
            throw $this->createNotFoundException("Invalid Treatment.");
        }

        $treatmentForm = new TreatmentFormType();
        $treatmentForm->setDoctrine($this->getDoctrine());

        $form = $this->createForm($treatmentForm, $treatment);

        $params = array(
            'form' => $form->createView(),
            'formAction' =>  $this->generateUrl('admin_treatment_update', array('id' => $treatment->getId())),
            'hasProcedures' => (bool)count($treatment->getTreatmentProcedures()),
            'treatment' => $treatment
        );

        return $this->render('AdminBundle:Treatment:form.html.twig', $params);
    }

    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_PROCEDURE_TYPE')")
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
            $treatment = $em->getRepository('MedicalProcedureBundle:Treatment')->find($id);
            if(!$treatment) {
                throw $this->createNotFoundException("Invalid Treatment.");
            }
        } 
        else {
            $treatment = new Treatment();
        }

        $treatmentForm = new TreatmentFormType();
        $treatmentForm->setDoctrine($this->getDoctrine());

        $form = $this->createForm($treatmentForm, $treatment);
        $form->bind($request);

        if ($form->isValid()) {
            $em->persist($treatment);
            $em->flush($treatment);
    
            $eventName = $id ? AdminBundleEvents::ON_EDIT_MEDICAL_PROCEDURE_TYPE : AdminBundleEvents::ON_ADD_MEDICAL_PROCEDURE_TYPE;
            $this->get('event_dispatcher')->dispatch($eventName, $this->get('events.factory')->create($eventName, $treatment));
            
            $request->getSession()->setFlash('success', $id ? "Successfully updated {$treatment->getName()}." : "Successfully added {$treatment->getName()}.");

            if($request->get('submit') == 'Save')
                return $this->redirect($this->generateUrl('admin_treatment_edit', array('id' => $treatment->getId())));
            else {
                $specializationId = $request->get('specializationId');
                $addParams = $specializationId ? array('specializationId' => $specializationId) : array();

                return $this->redirect($this->generateUrl('admin_treatment_add', $addParams));
            }

        } else {

            if(!$treatment->getId()) {
                $specializationId = $request->get('specializationId');
                $formAction = $this->generateUrl('admin_treatment_create', $specializationId ? array('specializationId' => $specializationId) : array());
            } else {
                $formAction = $this->generateUrl('admin_treatment_update', array('id' => $treatment->getId()));
            }

            $params = array(
                'form' => $form->createView(),
                'formAction' => $formAction,
                   'hasProcedures' => (bool)count($treatment->getTreatmentProcedures())
            );
            return $this->render('AdminBundle:Treatment:form.html.twig', $params);
        }
    }
    
    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_DELETE_PROCEDURE_TYPE')")
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function updateStatusAction($id)
    {
        $result = false;
        $treatment = $this->get('services.medical_procedure')->getTreatment($id);

        if($treatment) {
            $em = $this->getDoctrine()->getEntityManager();
            
            $status = $treatment->getStatus() == Treatment::STATUS_ACTIVE
                    ? Treatment::STATUS_INACTIVE 
                    : Treatment::STATUS_ACTIVE;

            $treatment->setStatus($status);
            $em->persist($treatment);
            $em->flush($treatment);
            
            // dispatch event
            $this->get('event_dispatcher')->dispatch(AdminBundleEvents::ON_EDIT_MEDICAL_PROCEDURE_TYPE, $this->get('events.factory')->create(AdminBundleEvents::ON_EDIT_MEDICAL_PROCEDURE_TYPE, $treatment));
            
            $result = true;
        }

        $response = new Response(json_encode($result));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}