<?php

namespace HealthCareAbroad\AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use HealthCareAbroad\MedicalProcedureBundle\Entity\MedicalProcedure;
use HealthCareAbroad\MedicalProcedureBundle\Form\MedicalProcedureType as MedicalProcedureForm;

class MedicalProcedureController extends Controller
{

    public function indexAction()
    {
		$procedures = $this->getDoctrine()->getEntityManager()->getRepository('MedicalProcedureBundle:MedicalProcedure')->findAll();
    	$data = array('procedures'=>$procedures);
    	return $this->render('AdminBundle:MedicalProcedure:index.html.twig', $data);
    }

    public function addAction()
    {
    	$em = $this->getDoctrine()->getEntityManager();
    	$procedure = new MedicalProcedure();
    	$form = $this->createForm(new MedicalProcedureForm(), $procedure);
    	$params = array('form' => $form->createView());

    	return $this->render('AdminBundle:MedicalProcedure:create.html.twig', $params);
    }
    
    public function editAction($id)
    {
    	$procedure = $this->get('services.medical_procedure')->getMedicalProcedure($id);
    	$form = $this->createForm(new MedicalProcedureForm($id), $procedure);
    	$params = array('form' => $form->createView());
    	return $this->render('AdminBundle:MedicalProcedure:create.html.twig', $params);
    }

    public function saveAction()
    {
    	$request = $this->getRequest();
    	
    	if ('POST' == $request->getMethod()) {
    		$data = $request->get('medicalProcedure');
    		$em = $this->getDoctrine()->getEntityManager();

			$procedure = $data['id']
				? $em->getRepository('MedicalProcedureBundle:MedicalProcedure')->find($data) 
				: new MedicalProcedure();

			$form = $this->createForm(new MedicalProcedureForm($em), $procedure);
    		$form->bind($request);

			if ($form->isValid()) {
				$this->get('services.medical_procedure')->saveMedicalProcedure($form->getData());

    			$request->getSession()->setFlash('notice', 'New Procedure has been added!');
    			return $this->redirect($this->generateUrl('admin_medicalProcedure_index'));
			}
	
    	}
    }
    
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
