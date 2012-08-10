<?php

namespace HealthCareAbroad\AdminBundle\Controller;

use Symfony\Component\Routing\Exception\MethodNotAllowedException;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use HealthCareAbroad\MedicalProcedureBundle\Entity\MedicalProcedure;
use HealthCareAbroad\MedicalProcedureBundle\Form\MedicalProcedureType as MedicalProcedureForm;
use JMS\SecurityExtraBundle\Annotation\PreAuthorize;

class MedicalProcedureController extends Controller
{

    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_VIEW_MEDICAL_PROCEDURES')")
     */
    public function indexAction()
    {
		$procedures = $this->getDoctrine()->getEntityManager()->getRepository('MedicalProcedureBundle:MedicalProcedure')->findAll();
    	$data = array('procedures'=>$procedures);
    	return $this->render('AdminBundle:MedicalProcedure:index.html.twig', $data);
    }

    /**
     * 
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_MEDICAL_PROCEDURE')")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addAction()
    {
    	$em = $this->getDoctrine()->getEntityManager();
    	$procedure = new MedicalProcedure();
    	$form = $this->createForm(new MedicalProcedureForm(), $procedure);
    	$params = array('form' => $form->createView(), 'id' => null);

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
    	$form = $this->createForm(new MedicalProcedureForm(), $procedure);
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

		$form = $this->createForm(new MedicalProcedureForm(), $procedure);
		$form->bind($request);

		if ($form->isValid()) {
			$this->get('services.medical_procedure')->saveMedicalProcedure($form->getData());

			$request->getSession()->setFlash('noticeType', 'success');
			$request->getSession()->setFlash('notice', 'New Procedure has been added!');
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
			$status = $procedure->getStatus() == MedicalProcedure::$STATUS['active'] 
					? MedicalProcedure::$STATUS['inactive'] 
					: MedicalProcedure::$STATUS['active'];

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
