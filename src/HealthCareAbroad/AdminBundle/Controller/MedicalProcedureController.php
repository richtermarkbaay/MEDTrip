<?php

namespace HealthCareAbroad\AdminBundle\Controller;

use HealthCareAbroad\AdminBundle\Event\AdminBundleEvents;

use HealthCareAbroad\HelperBundle\Services\Filters\ListFilter;

use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Routing\Exception\MethodNotAllowedException;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use HealthCareAbroad\MedicalProcedureBundle\Entity\MedicalProcedure;
use HealthCareAbroad\MedicalProcedureBundle\Form\MedicalProcedureFormType;
use JMS\SecurityExtraBundle\Annotation\PreAuthorize;

class MedicalProcedureController extends Controller
{

    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_VIEW_MEDICAL_PROCEDURES')")
     */
    public function indexAction(Request $request)
    {
        $medicalProcedureTypeId = $request->get('medicalProcedureType', 0);
        if ($medicalProcedureTypeId == ListFilter::FILTER_KEY_ALL) {
            $medicalProcedureTypeId = 0;
        }

        $params = array('medicalProcedureTypeId' => $medicalProcedureTypeId,'procedures' => $this->filteredResult, 'pager' => $this->pager);

        return $this->render('AdminBundle:MedicalProcedure:index.html.twig', $params);
    }

    /**
     * 
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_MEDICAL_PROCEDURE')")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addAction()
    {
        $params = $formActionParams = array();
        $procedure = new MedicalProcedure();

        if($medicalProcedureTypeId = $this->getRequest()->get('medicalProcedureTypeId', 0)) {
            $medicalProcedureType = $this->getDoctrine()->getRepository('MedicalProcedureBundle:MedicalProcedureType')->find($medicalProcedureTypeId);

            if(!$medicalProcedureType) {
                throw $this->createNotFoundException("Invalid Medical Procedure Type.");
            }

            $procedure->setMedicalProcedureType($medicalProcedureType);

            $params['isAddFromSpecificType'] = true;
            $formActionParams['medicalProcedureTypeId'] = $medicalProcedureTypeId;
        }

        $medicalProcedureForm = new MedicalProcedureFormType();
        $medicalProcedureForm->setDoctrine($this->getDoctrine());

        $form = $this->createForm($medicalProcedureForm, $procedure);        

        $params['form'] = $form->createView();
        $params['formAction'] = $this->generateUrl('admin_medicalProcedure_create', $formActionParams);
        return $this->render('AdminBundle:MedicalProcedure:form.html.twig', $params);
    }
    
    /**
     * 
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_MEDICAL_PROCEDURE')")
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction($id)
    {
        if($id) {
            $procedure = $this->get('services.medical_procedure')->getMedicalProcedure($id);
            if(!$procedure) {
                throw $this->createNotFoundException("Invalid Medical Procedure.");
            }
        }

        $medicalProcedureForm = new MedicalProcedureFormType();
        $medicalProcedureForm->setDoctrine($this->getDoctrine());

        $form = $this->createForm($medicalProcedureForm, $procedure);

        $params['form'] = $form->createView();
        $params['formAction'] = $this->generateUrl('admin_medicalProcedure_update', array('id' => $procedure->getId()));
        $params['hasInstitutionMedicalProcedures'] = (bool)count($procedure->getInstitutionMedicalProcedures()); // TODO - should be replaced with SELECT count(*) query
        return $this->render('AdminBundle:MedicalProcedure:form.html.twig', $params);
    }

    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_MEDICAL_PROCEDURE')")
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
            $procedure = $em->getRepository('MedicalProcedureBundle:MedicalProcedure')->find($id);

            if(!$procedure) throw $this->createNotFoundException("Invalid Medical Procedure.");
            
        } else $procedure = new MedicalProcedure();


        $medicalProcedureForm = new MedicalProcedureFormType();
        $medicalProcedureForm->setDoctrine($this->getDoctrine());

        $form = $this->createForm($medicalProcedureForm, $procedure);
        $form->bind($request);

        if ($form->isValid()) {
            $em->persist($procedure);
            $em->flush($procedure);

            // dispatch event
            $eventName = $id ? AdminBundleEvents::ON_EDIT_MEDICAL_PROCEDURE : AdminBundleEvents::ON_ADD_MEDICAL_PROCEDURE;
            $this->get('event_dispatcher')->dispatch($eventName, $this->get('events.factory')->create($eventName, $procedure));
            
            $request->getSession()->setFlash('success', 'Medical Procedure has been saved!');
            
            if($request->get('submit') == 'Save')
                return $this->redirect($this->generateUrl('admin_medicalProcedure_edit', array('id' => $procedure->getId())));
            else {
                $medicalProcedureTypeId = $request->get('medicalProcedureTypeId');
                $addParams = $medicalProcedureTypeId ? array('medicalProcedureTypeId' => $medicalProcedureTypeId) : array();
            
                return $this->redirect($this->generateUrl('admin_medicalProcedure_add', $addParams));
            }

        } else {
            $medicalProcedureTypeId = $request->get('medicalProcedureTypeId');
            $params = $formCreateParams = array();

            if($medicalProcedureTypeId) {
                $params['isAddFromSpecificType'] = true;
                $formCreateParams['medicalProcedureTypeId'] = $medicalProcedureTypeId;
            }

            if(!$procedure->getId()) {
                $formAction = $this->generateUrl('admin_medicalProcedure_create', $formCreateParams);
            } else {
                $formAction = $this->generateUrl('admin_medicalProcedure_update', array('id' => $procedure->getId()));
            }

            $params['form'] = $form->createView();
            $params['formAction'] = $formAction;
            $params['hasInstitutionMedicalProcedures'] = (bool)count($procedure->getInstitutionMedicalProcedures()); // TODO Should be replaced with SELECT count(*)
            
            return $this->render('AdminBundle:MedicalProcedure:form.html.twig', $params);
        }
    }

    /**
     * 
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_DELETE_MEDICAL_PROCEDURE')")
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function updateStatusAction($id)
    {
        $result = false;
        $procedure = $this->get('services.medical_procedure')->getMedicalProcedure($id);

        if($procedure) {
            $em = $this->getDoctrine()->getEntityManager();
            $status = $procedure->getStatus() == MedicalProcedure::STATUS_ACTIVE 
                    ? MedicalProcedure::STATUS_INACTIVE
                    : MedicalProcedure::STATUS_ACTIVE;

            $procedure->setStatus($status);
            $em->persist($procedure);
            $em->flush($procedure);
            
            // dispatch event
            $this->get('event_dispatcher')->dispatch(AdminBundleEvents::ON_EDIT_MEDICAL_PROCEDURE, $this->get('events.factory')->create(AdminBundleEvents::ON_EDIT_MEDICAL_PROCEDURE, $procedure));
            
            $result = true;
        }

        $response = new Response(json_encode($result));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}