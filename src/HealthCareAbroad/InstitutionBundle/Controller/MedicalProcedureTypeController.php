<?php 

namespace HealthCareAbroad\InstitutionBundle\Controller;

use HealthCareAbroad\InstitutionBundle\Event\InstitutionMedicalProcedureEvents;

use HealthCareAbroad\InstitutionBundle\Event\CreateInstitutionMedicalProcedureEvent;

use HealthCareAbroad\InstitutionBundle\Event\InstitutionMedicalProcedureTypeEvents;

use HealthCareAbroad\InstitutionBundle\Event\CreateInstitutionMedicalProcedureTypeEvent;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter;

use HealthCareAbroad\InstitutionBundle\Form\InstitutionMedicalProcedureFormType;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalProcedure;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalProcedureType;

use HealthCareAbroad\InstitutionBundle\Form\InstitutionMedicalProcedureTypeFormType;

use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Response;
use JMS\SecurityExtraBundle\Annotation\PreAuthorize;

class MedicalProcedureTypeController extends InstitutionAwareController
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
        $institutionMedicalProcedureTypes = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionMedicalProcedureType')->findByInstitutionMedicalCenter(array($this->institutionMedicalCenter->getId()));
        $params = array(
            'institutionMedicalProcedureTypes' => $institutionMedicalProcedureTypes
        );
        return $this->render('InstitutionBundle:MedicalProcedureType:index.html.twig', $params);
    }
    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_PROCEDURE_TYPES')")
     */
    public function addAction(Request $request)
    {
        if (!$request->isXmlHttpRequest()) {
            //throw $this->createNotFoundException();
        }
        
        $institutionMedicalProcedureType = new InstitutionMedicalProcedureType();
        $institutionMedicalProcedureType->setInstitutionMedicalCenter($this->institutionMedicalCenter);
        $form = $this->createForm(new InstitutionMedicalProcedureTypeFormType(),$institutionMedicalProcedureType);
        //return $this->render('InstitutionBundle:Default:index.html.twig');
        return $this->render('InstitutionBundle:MedicalProcedureType:modalForm.html.twig', array(
            'form' => $form->createView(),
            'institutionMedicalProcedureType' => $institutionMedicalProcedureType,
            'newObject' => true
        ));
    }
    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_PROCEDURE_TYPES')")
     */
    public function editAction(Request $request)
    {
        if (!$request->isXmlHttpRequest()) {
            //throw $this->createNotFoundException();
        }
        
        $institutionMedicalProcedureType = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionMedicalProcedureType')->find($request->get('imptId', 0));
        if (!$institutionMedicalProcedureType) {
            throw $this->createNotFoundException('Invalid InstitutionMedicalProcedureType');
        }
        
        $form = $this->createForm(new InstitutionMedicalProcedureTypeFormType(),$institutionMedicalProcedureType);
        return $this->render('InstitutionBundle:MedicalProcedureType:modalForm.html.twig', array(
            'form' => $form->createView(),
            'institutionMedicalProcedureType' => $institutionMedicalProcedureType,
            'newObject' => false
        ));
    }
    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_PROCEDURE_TYPES')")
     */
    public function saveAction(Request $request)
    {
        if (!$request->isMethod('POST')) {
            return new Response('Unsupported method', 405);
        }
        
        if ($imptId = $request->get('imptId', 0)) {
            $institutionMedicalProcedureType = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionMedicalProcedureType')->find($imptId);
            if (!$institutionMedicalProcedureType) {
                throw $this->createNotFoundException('Invalid InstitutionMedicalProcedureType');
            }
        }
        else {
            $institutionMedicalProcedureType = new InstitutionMedicalProcedureType();
            $institutionMedicalProcedureType->setInstitutionMedicalCenter($this->institutionMedicalCenter);
        }
        
        $form = $this->createForm(new InstitutionMedicalProcedureTypeFormType(), $institutionMedicalProcedureType);
        $form->bindRequest($request);
        $isNew = $institutionMedicalProcedureType->getId() == 0;
        if ($form->isValid()){
            $institutionMedicalProcedureType = $form->getData();
            $institutionMedicalProcedureType->setStatus(InstitutionMedicalProcedureType::STATUS_ACTIVE);
            
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($institutionMedicalProcedureType);
            $em->flush($institutionMedicalProcedureType);
            
            if($isNew) {
	            //// create event on adding institutionMedicalProcedureTypes and dispatch
	            $event = new CreateInstitutionMedicalProcedureTypeEvent($institutionMedicalProcedureType);
	            $this->get('event_dispatcher')->dispatch(InstitutionMedicalProcedureTypeEvents::ON_ADD_INSTITUTION_MEDICAL_PROCEDURE_TYPE, $event);
            }
            else {
            	//// create event on editing institutionMedicalProcedureTypes and dispatch
            	$event = new CreateInstitutionMedicalProcedureTypeEvent($procedureType);
            	$this->get('event_dispatcher')->dispatch(InstitutionMedicalProcedureTypeEvents::ON_EDIT_INSTITUTION_MEDICAL_PROCEDURE_TYPE, $event);
            }
            $request->getSession()->setFlash('success', 'Successfully saved medical procedure type.');
            
            return $this->redirect($this->generateUrl('institution_medicalCenter_edit', array('imcId' => $this->institutionMedicalCenter->getId())));
        }
        
        return $this->render('InstitutionBundle:MedicalProcedureType:form.html.twig', array(
            'form' => $form->createView(),
            'institutionMedicalProcedureType' => $institutionMedicalProcedureType,
            'newObject' => $isNew
        ));
    }
    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_PROCEDURE_TYPES')")
     */
    public function addMedicalProcedureAction(Request $request)
    {
        $institutionMedicalProcedureType = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionMedicalProcedureType')->find($request->get('imptId', 0));
        if (!$institutionMedicalProcedureType) {
            throw $this->createNotFoundException('Invalid InstitutionMedicalProcedureType');
        }
        
        $institutionMedicalProcedure = new InstitutionMedicalProcedure();
        $institutionMedicalProcedure->setInstitutionMedicalProcedureType($institutionMedicalProcedureType);
        
        $form = $this->createForm(new InstitutionMedicalProcedureFormType(), $institutionMedicalProcedure);
        $params = array(
            'imptId' => $institutionMedicalProcedureType->getId(),
            'impId' => $institutionMedicalProcedure->getId(),
            'procedureTypeName' => $institutionMedicalProcedureType->getMedicalProcedureType()->getName(),
            'medicalCenterName' => $institutionMedicalProcedureType->getMedicalProcedureType()->getMedicalCenter()->getName(),
            'form' => $form->createView(),
        );
        return $this->render('InstitutionBundle:MedicalProcedureType:form.procedure.html.twig', $params);
        //return $this->render('InstitutionBundle:Default:index.html.twig');
    }
    
    public function editMedicalProcedureAction(Request $request)
    {
        $institutionMedicalProcedure = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionMedicalProcedure')->find($request->get('impId',0));
        if (!$institutionMedicalProcedure) {
            throw $this->createNotFoundException("Invalid institution medical procedure");
        }
        $institutionMedicalProcedureType = $institutionMedicalProcedure->getInstitutionMedicalProcedureType();
        $form = $this->createForm(new InstitutionMedicalProcedureFormType(), $institutionMedicalProcedure);
        $params = array(
            'imptId' => $institutionMedicalProcedureType->getId(),
            'impId' => $institutionMedicalProcedure->getId(),
            'procedureTypeName' => $institutionMedicalProcedureType->getMedicalProcedureType()->getName(),
            'medicalCenterName' => $institutionMedicalProcedureType->getMedicalProcedureType()->getMedicalCenter()->getName(),
            'form' => $form->createView()
        );
        return $this->render('InstitutionBundle:MedicalProcedureType:form.procedure.html.twig', $params);
    }
    
    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_PROCEDURE_TYPES')")
     */
    public function saveMedicalProcedureAction(Request $request)
    {
        $institutionMedicalProcedureType = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionMedicalProcedureType')->find($request->get('imptId', 0));
        if (!$institutionMedicalProcedureType) {
            throw $this->createNotFoundException('Invalid InstitutionMedicalProcedureType');
        }
        
        if ($impId = $request->get('impId',0)) {
            $institutionMedicalProcedure = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionMedicalProcedure')->find($impId);
            if (!$institutionMedicalProcedure) {
                throw $this->createNotFoundException("Invalid institution medical procedure");
            }
        }
        else {
            $institutionMedicalProcedure = new InstitutionMedicalProcedure();
            $institutionMedicalProcedure->setInstitutionMedicalProcedureType($institutionMedicalProcedureType);
        }
        
        $form = $this->createForm(new InstitutionMedicalProcedureFormType(), $institutionMedicalProcedure);
        $form->bindRequest($request);
        
        if ($form->isValid()) {

            $institutionMedicalProcedure = $form->getData();
            
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($institutionMedicalProcedure);
            $em->flush();
            
            if($isNew) {
            	//// create event on adding institutionMedicalProcedureTypes and dispatch
            	$event = new CreateInstitutionMedicalProcedureEvent($institutionMedicalProcedure);
            	$this->get('event_dispatcher')->dispatch(InstitutionMedicalProcedureEvents::ON_ADD_INSTITUTION_MEDICAL_PROCEDURE, $event);
            }
            else {
            	//// create event on editing institutionMedicalProcedureTypes and dispatch
            	$event = new CreateInstitutionMedicalProcedureTypeEvent($procedureType);
            	$this->get('event_dispatcher')->dispatch(InstitutionMedicalProcedureEvents::ON_EDIT_INSTITUTION_MEDICAL_PROCEDURE, $event);
            }
            
            $request->getSession()->setFlash('success', "Successfully added a medical procedure to {$institutionMedicalProcedureType->getMedicalProcedureType()->getName()} medical procedure type.");
            return $this->redirect($this->generateUrl('institution_medicalCenter_editProcedureType', array('imcId' => $institutionMedicalProcedureType->getInstitutionMedicalCenter()->getId(),'imptId' => $institutionMedicalProcedureType->getId())));
        }
        
        $params = array(
            'imptId' => $institutionMedicalProcedureType->getId(),
            'procedureTypeName' => $institutionMedicalProcedureType->getMedicalProcedureType()->getName(),
            'medicalCenterName' => $institutionMedicalProcedureType->getMedicalProcedureType()->getMedicalCenter()->getName(),
            'form' => $form->createView()
        );
        
        return $this->render('InstitutionBundle:MedicalProcedureType:form.procedure.html.twig', $params);
    }

	
    
    /** TODO: remove permanently
	function loadProceduresAction()
	{
		$request = $this->getRequest();
		$institutionId = $request->get('institution_id', $this->get('session')->get('institutionId'));
		$procedureTypeId = $request->get('procedure_type_id');

		$em = $this->getDoctrine()->getEntityManager();
		$procedureType = $em->getRepository('MedicalProcedureBundle:MedicalProcedureType')->find($procedureTypeId);
	
		$criteria = array('medicalProcedureType' => $procedureType, 'status' => 1);
		$procedures = $em->getRepository('MedicalProcedureBundle:MedicalProcedure')->findBy($criteria);
	
		$activeProcedureIds = $em->getRepository('InstitutionBundle:InstitutionMedicalProcedure')->getProcedureIdsByTypeId($institutionId, $procedureTypeId);

		$data = array();
		foreach($procedures as $each) {
			if(!in_array($each->getId(), $activeProcedureIds)) {
				$data[] = array('id' => $each->getId(), 'name' => $each->getName());
			}
		}

		$response = new Response(json_encode($data));
		$response->headers->set('Content-Type', 'application/json');

		return $response;
	}
	**/
}