<?php

namespace HealthCareAbroad\AdminBundle\Controller;

use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use HealthCareAbroad\MedicalProcedureBundle\Entity\MedicalProcedureType;
use HealthCareAbroad\MedicalProcedureBundle\Form\MedicalProcedureTypeFormType;
use JMS\SecurityExtraBundle\Annotation\PreAuthorize;

class MedicalProcedureTypeController extends Controller
{

    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_VIEW_PROCEDURE_TYPES')")
     */
    public function indexAction()
    {
		return $this->render('AdminBundle:MedicalProcedureType:index.html.twig', array('procedureTypes'=> $this->filteredResult));
    }

    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_PROCEDURE_TYPE')")
     */
    public function addAction()
    {
    	$params = $formActionParams = array();
    	$procedureType = new MedicalProcedureType();

    	$medicalCenterId = $this->getRequest()->get('medicalCenterId');

    	if($medicalCenterId) {
    		$medicalCenter = $this->getDoctrine()->getRepository('MedicalProcedureBundle:MedicalCenter')->find($medicalCenterId);
    		
    		if(!$medicalCenter) {
    			throw $this->createNotFoundException("Invalid Medical Center.");
    		}

    		$procedureType->setMedicalCenter($medicalCenter);

    		$params['isAddFromSpecificCenter'] = true;
    		$formActionParams['medicalCenterId'] = $medicalCenterId;
    	}

    	$form = $this->createForm(new MedicalProcedureTypeFormType(), $procedureType);

    	$params['form'] = $form->createView();
    	$params['formAction'] = $this->generateUrl('admin_procedureType_create', $formActionParams);

    	return $this->render('AdminBundle:MedicalProcedureType:form.html.twig', $params);
    }
    
    /**
     * 
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_PROCEDURE_TYPE')")
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction($id)
    {
    	$procedureType = $this->getDoctrine()->getRepository('MedicalProcedureBundle:MedicalProcedureType')->find($id);

    	if(!$procedureType) {
			throw $this->createNotFoundException("Invalid Medical Procedure Type.");
    	}

    	$form = $this->createForm(new MedicalProcedureTypeFormType(), $procedureType);
    	$params = array(
    		'form' => $form->createView(),
    		'formAction' =>  $this->generateUrl('admin_procedureType_update', array('id' => $procedureType->getId())),
    		'hasProcedures' => (bool)count($procedureType->getMedicalProcedures()),
   			'medicalProcedureType' => $procedureType
    	);

    	return $this->render('AdminBundle:MedicalProcedureType:form.html.twig', $params);
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
			$procedureType = $em->getRepository('MedicalProcedureBundle:MedicalProcedureType')->find($id);
			if(!$procedureType) {
				throw $this->createNotFoundException("Invalid Medical Procedure Type.");
			}
		} else $procedureType = new MedicalProcedureType();

		$form = $this->createForm(new MedicalProcedureTypeFormType(), $procedureType);
		$form->bind($request);

		if ($form->isValid()) {
			$em->persist($procedureType);
			$em->flush($procedureType);

			$request->getSession()->setFlash('success', 'New Procedure Type has been added!');

			if($request->get('submit') == 'Save')
				return $this->redirect($this->generateUrl('admin_procedureType_edit', array('id' => $procedureType->getId())));
			else {
				$medicalCenterId = $request->get('medicalCenterId');
				$addParams = $medicalCenterId ? array('medicalCenterId' => $medicalCenterId) : array();

				return $this->redirect($this->generateUrl('admin_procedureType_add', $addParams));
			}

		} else {

			if(!$procedureType->getId()) {
				$medicalCenterId = $request->get('medicalCenterId');
				$formAction = $this->generateUrl('admin_procedureType_create', $medicalCenterId ? array('medicalCenterId' => $medicalCenterId) : array());
			} else {
				$formAction = $this->generateUrl('admin_procedureType_update', array('id' => $procedureType->getId()));
			}

	    	$params = array(
	    		'form' => $form->createView(),
	    		'formAction' => $formAction,
	   			'hasProcedures' => (bool)count($procedureType->getMedicalProcedures())
	    	);
	    	return $this->render('AdminBundle:MedicalProcedureType:form.html.twig', $params);
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
		$procedureType = $this->get('services.medical_procedure')->getMedicalProcedureType($id);

		if($procedureType) {
			$em = $this->getDoctrine()->getEntityManager();
			
			$status = $procedureType->getStatus() == MedicalProcedureType::STATUS_ACTIVE
					? MedicalProcedureType::STATUS_INACTIVE 
					: MedicalProcedureType::STATUS_ACTIVE;

			$procedureType->setStatus($status);
			$em->persist($procedureType);
			$em->flush($procedureType);
			$result = true;
		}

		$response = new Response(json_encode($result));
		$response->headers->set('Content-Type', 'application/json');

		return $response;
    }
}
