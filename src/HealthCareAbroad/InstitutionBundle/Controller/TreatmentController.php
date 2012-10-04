<?php 

namespace HealthCareAbroad\InstitutionBundle\Controller;

use HealthCareAbroad\InstitutionBundle\Event\InstitutionBundleEvents;

use HealthCareAbroad\InstitutionBundle\Event\InstitutionTreatmentProcedureEvents;

use HealthCareAbroad\InstitutionBundle\Event\CreateInstitutionTreatmentProcedureEvent;

use HealthCareAbroad\InstitutionBundle\Event\InstitutionTreatmentEvents;

use HealthCareAbroad\InstitutionBundle\Event\CreateInstitutionTreatmentEvent;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter;

use HealthCareAbroad\InstitutionBundle\Form\InstitutionTreatmentProcedureFormType;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionTreatmentProcedure;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionTreatment;

use HealthCareAbroad\InstitutionBundle\Form\InstitutionTreatmentFormType;

use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Response;
use JMS\SecurityExtraBundle\Annotation\PreAuthorize;

class TreatmentController extends InstitutionAwareController
{
    /**
     * @var InstitutionMedicalCenter
     */
    private $institutionMedicalCenter;
    
    public function preExecute()
    {
        $imcId = $this->getRequest()->get('imcId', 0);
        if ($imcId) {
            $this->institutionMedicalCenter = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionMedicalCenter')->find($imcId);
            if (!$this->institutionMedicalCenter) {
                throw $this->createNotFoundException("Invalid institution medical center");
            }    
        }
    }
    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_VIEW_PROCEDURE_TYPES')")
     */
    public function indexAction(Request $request)
    {
        $institutionTreatments = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionTreatment')->findByInstitutionMedicalCenter(array($this->institutionMedicalCenter->getId()));
        $params = array(
            'institutionTreatments' => $institutionTreatments
        );
        return $this->render('InstitutionBundle:Treatment:index.html.twig', $params);
    }
    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_PROCEDURE_TYPES')")
     */
    public function addAction(Request $request)
    {
        if (!$request->isXmlHttpRequest()) {
            //throw $this->createNotFoundException();
        }
        
        $institutionTreatment = new InstitutionTreatment();
        $institutionTreatment->setInstitutionMedicalCenter($this->institutionMedicalCenter);
        $form = $this->createForm(new InstitutionTreatmentFormType(),$institutionTreatment);
        //return $this->render('InstitutionBundle:Default:index.html.twig');
        return $this->render('InstitutionBundle:Treatment:modalForm.html.twig', array(
            'form' => $form->createView(),
            'institutionTreatment' => $institutionTreatment,
            'newObject' => true
        ));
    }
    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_PROCEDURE_TYPES')")
     */
    public function editAction(Request $request)
    {
        if (!$request->isXmlHttpRequest()) {
            throw $this->createNotFoundException();
        }
        
        $institutionTreatment = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionTreatment')->find($request->get('imptId', 0));
        if (!$institutionTreatment) {
            throw $this->createNotFoundException('Invalid InstitutionTreatment');
        }
        
        $form = $this->createForm(new InstitutionTreatmentFormType(),$institutionTreatment);
        return $this->render('InstitutionBundle:Treatment:modalForm.html.twig', array(
            'form' => $form->createView(),
            'institutionTreatment' => $institutionTreatment,
            'newObject' => false
        ));
    }
    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_PROCEDURE_TYPES')")
     */
    public function saveAction(Request $request)
    {
        if (!$request->isXmlHttpRequest()) {
            throw $this->createNotFoundException();
        }

        if (!$request->isMethod('POST')) {
            return new Response('Unsupported method', 405);
        }
        
        if ($imptId = $request->get('imptId', 0)) {
            $institutionTreatment = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionTreatment')->find($imptId);
            if (!$institutionTreatment) {
                throw $this->createNotFoundException('Invalid InstitutionTreatment');
            }
        }
        else {
            $institutionTreatment = new InstitutionTreatment();
            $institutionTreatment->setInstitutionMedicalCenter($this->institutionMedicalCenter);
        }
        
        $form = $this->createForm(new InstitutionTreatmentFormType(), $institutionTreatment);
        $form->bindRequest($request);
        $isNew = $institutionTreatment->getId() == 0;
        if ($form->isValid()){
            $institutionTreatment = $form->getData();
            $institutionTreatment->setStatus(InstitutionTreatment::STATUS_ACTIVE);
            
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($institutionTreatment);
            $em->flush($institutionTreatment);
            
            // dispatch event
            $eventName = $isNew ? InstitutionBundleEvents::ON_ADD_INSTITUTION_TREATMENT : InstitutionBundleEvents::ON_EDIT_INSTITUTION_TREATMENT; 
            $this->get('event_dispatcher')->dispatch($eventName, $this->get('events.factory')->create($eventName, $institutionTreatment));
            
            $request->getSession()->setFlash('success', 'Successfully saved treatment.');
            
            $params = array('imcId' => $this->institutionMedicalCenter->getId());
            
            if($request->get('imptId'))
                $params['imptId'] = $request->get('imptId');
            
            $url = $this->generateUrl('institution_medicalCenter_edit', $params);
            
    		$response = new Response(json_encode(array('redirect_url' => $url)));
    		$response->headers->set('Content-Type', 'application/json');
    
    		return $response;
        } else {

            return $this->render('InstitutionBundle:Treatment:modalForm.html.twig', array(
                'form' => $form->createView(),
                'institutionTreatment' => $institutionTreatment,
                'newObject' => $isNew
            ));            
        }
    }
    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_PROCEDURE_TYPES')")
     */
    public function addMedicalProcedureAction(Request $request)
    {
        if(!$request->isXmlHttpRequest()) {
            throw $this->createNotFoundException();
        }

        $institutionTreatment = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionTreatment')->find($request->get('imptId', 0));
        if (!$institutionTreatment) {
            throw $this->createNotFoundException('Invalid InstitutionTreatment');
        }
        
        $institutionMedicalProcedure = new InstitutionTreatmentProcedure();
        $institutionMedicalProcedure->setInstitutionTreatment($institutionTreatment);
        
        $form = $this->createForm(new InstitutionTreatmentProcedureFormType(), $institutionMedicalProcedure);
        $params = array(
            'imptId' => $institutionTreatment->getId(),
            'impId' => $institutionMedicalProcedure->getId(),
            'procedureTypeName' => $institutionTreatment->getTreatment()->getName(),
            'medicalCenterName' => $institutionTreatment->getTreatment()->getMedicalCenter()->getName(),
            'form' => $form->createView(),
        );

        return $this->render('InstitutionBundle:Treatment:modalForm.procedure.html.twig', $params);
    }
    
    public function editMedicalProcedureAction(Request $request)
    {
        if(!$request->isXmlHttpRequest()) {
            throw $this->createNotFoundException();
        }

        $institutionMedicalProcedure = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionTreatmentProcedure')->find($request->get('impId',0));
        if (!$institutionMedicalProcedure) {
            throw $this->createNotFoundException("Invalid institution medical procedure");
        }
        $institutionTreatment = $institutionMedicalProcedure->getInstitutionTreatment();
        $form = $this->createForm(new InstitutionTreatmentProcedureFormType(), $institutionMedicalProcedure);
        $params = array(
            'imptId' => $institutionTreatment->getId(),
            'impId' => $institutionMedicalProcedure->getId(),
            'procedureTypeName' => $institutionTreatment->getTreatment()->getName(),
            'medicalCenterName' => $institutionTreatment->getTreatment()->getMedicalCenter()->getName(),
            'form' => $form->createView()
        );
        return $this->render('InstitutionBundle:Treatment:modalForm.procedure.html.twig', $params);
    }
    
    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_PROCEDURE_TYPES')")
     */
    public function saveMedicalProcedureAction(Request $request)
    {
        if(!$request->isXmlHttpRequest()) {
            throw $this->createNotFoundException();
        }

        $institutionTreatment = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionTreatment')->find($request->get('imptId', 0));
        if (!$institutionTreatment) {
            throw $this->createNotFoundException('Invalid InstitutionTreatment');
        }
        
        if ($impId = $request->get('impId',0)) {
            $institutionMedicalProcedure = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionTreatmentProcedure')->find($impId);
            if (!$institutionMedicalProcedure) {
                throw $this->createNotFoundException("Invalid institution medical procedure");
            }
        }
        else {
            $institutionMedicalProcedure = new InstitutionTreatmentProcedure();
            $institutionMedicalProcedure->setInstitutionTreatment($institutionTreatment);
        }
        
        $form = $this->createForm(new InstitutionTreatmentProcedureFormType(), $institutionMedicalProcedure);
        $form->bind($request);
        
        if ($form->isValid()) {

            $institutionMedicalProcedure = $form->getData();
            
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($institutionMedicalProcedure);
            $em->flush();
            
            if($impId) {
            	//// create event on adding institutionTreatments and dispatch
            	$event = new CreateInstitutionTreatmentProcedureEvent($institutionMedicalProcedure);
            	$this->get('event_dispatcher')->dispatch(InstitutionBundleEvents::ON_ADD_INSTITUTION_MEDICAL_PROCEDURE, $event);
            }
            else {
            	//// create event on editing institutionTreatments and dispatch
            	$event = new CreateInstitutionTreatmentEvent($procedureType);
            	$this->get('event_dispatcher')->dispatch(InstitutionBundleEvents::ON_EDIT_INSTITUTION_MEDICAL_PROCEDURE, $event);
            }
            
            $request->getSession()->setFlash('success', "Successfully added a medical procedure to {$institutionTreatment->getTreatment()->getName()} treatment.");
            $url = $this->generateUrl('institution_medicalCenter_edit', array('imcId' => $institutionTreatment->getInstitutionMedicalCenter()->getId(),'imptId' => $institutionTreatment->getId()));
            
            $response = new Response(json_encode(array('redirect_url' => $url)));
            $response->headers->set('Content-Type', 'application/json');
            
            return $response;
        }
        
        $params = array(
            'imptId' => $institutionTreatment->getId(),
            'procedureTypeName' => $institutionTreatment->getTreatment()->getName(),
            'medicalCenterName' => $institutionTreatment->getTreatment()->getMedicalCenter()->getName(),
            'form' => $form->createView()
        );

        return $this->render('InstitutionBundle:Treatment:modalForm.procedure.html.twig', $params);
    }
}