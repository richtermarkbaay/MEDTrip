<?php

namespace HealthCareAbroad\AdminBundle\Controller;

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
    public function indexAction()
    {
    	return $this->render('AdminBundle:MedicalProcedure:index.html.twig', array('procedures' => $this->filteredResult));
    }

    /**
     * 
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_MEDICAL_PROCEDURE')")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addAction()
    {
    	$procedure = new MedicalProcedure();
    	
    	
    	if($medicalProcedureTypeId = $this->getRequest()->get('medicalProcedureTypeId')) {
    		$medicalProcedureType = $this->getDoctrine()->getRepository('MedicalProcedureBundle:MedicalProcedureType')->find($medicalProcedureTypeId);
    		$procedure->setMedicalProcedureType($medicalProcedureType);
    	}

    	$form = $this->createForm(new MedicalProcedureFormType(), $procedure);
    	$params = array('form' => $form->createView(), 'id' => null);

    	if($medicalProcedureTypeId)
    		$params['isAddFromSpecificType'] = true;
    	
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
    	$procedure = $this->get('services.medical_procedure')->getMedicalProcedure($id);
    	$form = $this->createForm(new MedicalProcedureFormType(), $procedure);
    	$params = array('form' => $form->createView(), 'id' => $id);
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

		$procedure = $id
			? $em->getRepository('MedicalProcedureBundle:MedicalProcedure')->find($id) 
			: new MedicalProcedure();

		$form = $this->createForm(new MedicalProcedureFormType(), $procedure);
		$form->bind($request);

		if ($form->isValid()) {
			$em->persist($procedure);
			$em->flush($procedure);

			$request->getSession()->setFlash('success', 'New Procedure has been added!');
			return $this->redirect($this->generateUrl('admin_medicalProcedure_index'));
		} else {
			$params = array('form' => $form->createView(), 'id' => $id);
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
			$result = true;
		}

		$response = new Response(json_encode($result));
		$response->headers->set('Content-Type', 'application/json');

		return $response;
    }
}
