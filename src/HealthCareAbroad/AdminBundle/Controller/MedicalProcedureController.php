<?php

namespace HealthCareAbroad\AdminBundle\Controller;

use HealthCareAbroad\AdminBundle\Event\AdminBundleEvents;

use HealthCareAbroad\HelperBundle\Services\Filters\ListFilter;

use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Routing\Exception\MethodNotAllowedException;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use HealthCareAbroad\MedicalProcedureBundle\Entity\TreatmentProcedure;
use HealthCareAbroad\MedicalProcedureBundle\Form\TreatmentProcedureFormType;
use JMS\SecurityExtraBundle\Annotation\PreAuthorize;

class MedicalProcedureController extends Controller
{

    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_VIEW_MEDICAL_PROCEDURES')")
     */
    public function indexAction(Request $request)
    {
        $treatmentId = $request->get('treatment', 0);
        if ($treatmentId == ListFilter::FILTER_KEY_ALL) {
            $treatmentId = 0;
        }

        $params = array('treatmentId' => $treatmentId,'procedures' => $this->filteredResult, 'pager' => $this->pager);

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
        $procedure = new TreatmentProcedure();

        if($treatmentId = $this->getRequest()->get('treatmentId', 0)) {
            $treatment = $this->getDoctrine()->getRepository('MedicalProcedureBundle:Treatment')->find($treatmentId);

            if(!$treatment) {
                throw $this->createNotFoundException("Invalid Treatment.");
            }

            $procedure->setTreatment($treatment);

            $params['isAddFromSpecificType'] = true;
            $formActionParams['treatmentId'] = $treatmentId;
        }

        $treatmentProcedureForm = new TreatmentProcedureFormType();
        $treatmentProcedureForm->setDoctrine($this->getDoctrine());

        $form = $this->createForm($treatmentProcedureForm, $procedure);        

        $params['form'] = $form->createView();
        $params['formAction'] = $this->generateUrl('admin_treatmentProcedure_create', $formActionParams);
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
            $procedure = $this->get('services.medical_procedure')->getTreatmentProcedure($id);
            if(!$procedure) {
                throw $this->createNotFoundException("Invalid Treatment Procedure.");
            }
        }

        $treatmentProcedureForm = new TreatmentProcedureFormType();
        $treatmentProcedureForm->setDoctrine($this->getDoctrine());

        $form = $this->createForm($treatmentProcedureForm, $procedure);

        $params['form'] = $form->createView();
        $params['formAction'] = $this->generateUrl('admin_treatmentProcedure_update', array('id' => $procedure->getId()));
        $params['hasInstitutionTreatmentProcedures'] = (bool)count($procedure->getInstitutionTreatmentProcedures()); // TODO - should be replaced with SELECT count(*) query
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
            $procedure = $em->getRepository('MedicalProcedureBundle:TreatmentProcedure')->find($id);

            if(!$procedure) throw $this->createNotFoundException("Invalid Treatment Procedure.");
            
        } else $procedure = new TreatmentProcedure();


        $treatmentProcedureForm = new TreatmentProcedureFormType();
        $treatmentProcedureForm->setDoctrine($this->getDoctrine());

        $form = $this->createForm($treatmentProcedureForm, $procedure);
        $form->bind($request);

        if ($form->isValid()) {
            $em->persist($procedure);
            $em->flush($procedure);

            // dispatch event
            $eventName = $id ? AdminBundleEvents::ON_EDIT_MEDICAL_PROCEDURE : AdminBundleEvents::ON_ADD_MEDICAL_PROCEDURE;
            $this->get('event_dispatcher')->dispatch($eventName, $this->get('events.factory')->create($eventName, $procedure));
            
            $request->getSession()->setFlash('success', 'Treatment Procedure has been saved!');
            
            if($request->get('submit') == 'Save')
                return $this->redirect($this->generateUrl('admin_treatmentProcedure_edit', array('id' => $procedure->getId())));
            else {
                $treatmentId = $request->get('treatmentId');
                $addParams = $treatmentId ? array('treatmentId' => $treatmentId) : array();
            
                return $this->redirect($this->generateUrl('admin_treatmentProcedure_add', $addParams));
            }

        } else {
            $treatmentId = $request->get('treatmentId');
            $params = $formCreateParams = array();

            if($treatmentId) {
                $params['isAddFromSpecificType'] = true;
                $formCreateParams['treatmentId'] = $treatmentId;
            }

            if(!$procedure->getId()) {
                $formAction = $this->generateUrl('admin_treatmentProcedure_create', $formCreateParams);
            } else {
                $formAction = $this->generateUrl('admin_treatmentProcedure_update', array('id' => $procedure->getId()));
            }

            $params['form'] = $form->createView();
            $params['formAction'] = $formAction;
            $params['hasInstitutionTreatmentProcedures'] = (bool)count($procedure->getInstitutionTreatmentProcedures()); // TODO Should be replaced with SELECT count(*)
            
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
        $procedure = $this->get('services.medical_procedure')->getTreatmentProcedure($id);

        if($procedure) {
            $em = $this->getDoctrine()->getEntityManager();
            $status = $procedure->getStatus() == TreatmentProcedure::STATUS_ACTIVE 
                    ? TreatmentProcedure::STATUS_INACTIVE
                    : TreatmentProcedure::STATUS_ACTIVE;

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