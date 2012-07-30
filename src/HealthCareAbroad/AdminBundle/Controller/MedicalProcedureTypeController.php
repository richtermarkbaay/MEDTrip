<?php

namespace HealthCareAbroad\AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use HealthCareAbroad\MedicalProcedureBundle\Entity\MedicalProcedureType;
use HealthCareAbroad\MedicalProcedureBundle\Form\MedicalProcedureTypeType as MedicalProcedureTypeForm;

class MedicalProcedureTypeController extends Controller
{

    public function indexAction()
    {
		$procedureTypes = $this->getDoctrine()->getEntityManager()->getRepository('MedicalProcedureBundle:MedicalProcedureType')->findAll();
    	$data = array('procedureTypes'=>$procedureTypes);
    	return $this->render('AdminBundle:MedicalProcedureType:index.html.twig', $data);
    }

    public function addAction()
    {
    	$em = $this->getDoctrine()->getEntityManager();
    	$procedureType = new MedicalProcedureType();
    	$form = $this->createForm(new MedicalProcedureTypeForm($em), $procedureType);
    	$params = array('form' => $form->createView());

    	return $this->render('AdminBundle:MedicalProcedureType:create.html.twig', $params);
    }
    
    public function editAction($id)
    {
    	$em = $this->getDoctrine()->getEntityManager();
    	$procedureType = $this->get('services.medical_procedure')->getMedicalProcedureType($id);
    	$form = $this->createForm(new MedicalProcedureTypeForm($em, $id), $procedureType);
    	$params = array('form' => $form->createView());
    	return $this->render('AdminBundle:MedicalProcedureType:create.html.twig', $params);
    }

    public function saveAction()
    {
    	$request = $this->getRequest();
    	
    	if ('POST' == $request->getMethod()) {
    		$data = $request->get('medicalProcedureType');
    		$em = $this->getDoctrine()->getEntityManager();

			$procedureType = $data['id']
				? $em->getRepository('MedicalProcedureBundle:MedicalProcedureType')->find($data) 
				: new MedicalProcedureType();

			$form = $this->createForm(new MedicalProcedureTypeForm($em), $procedureType);
    		$form->bind($request);

			if ($form->isValid()) {
				$procedureType = $form->getData();
				$procedureType->setSlug('');
				$em->persist($procedureType);
				$em->flush($procedureType);

    			$request->getSession()->setFlash('notice', 'New Procedure Type has been added!');
    			return $this->redirect($this->generateUrl('admin_procedureType_index'));
			}
    	}
    }
    
    public function updateStatusAction($id)
    {
    	$result = false;
		$procedureType = $this->get('services.medical_procedure')->getMedicalProcedureType($id);

		if($procedureType) {
			$em = $this->getDoctrine()->getEntityManager();
			$status = $procedureType->getStatus() == MedicalProcedureType::$STATUS['active'] 
					? MedicalProcedureType::$STATUS['inactive'] 
					: MedicalProcedureType::$STATUS['active'];

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
