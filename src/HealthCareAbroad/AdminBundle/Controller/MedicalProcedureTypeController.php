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
    	$params = array('form' => $form->createView(), 'id' => null);

    	return $this->render('AdminBundle:MedicalProcedureType:form.html.twig', $params);
    }
    
    public function editAction($id)
    {
    	$em = $this->getDoctrine()->getEntityManager();
    	$procedureType = $this->get('services.medical_procedure')->getMedicalProcedureType($id);
    	$form = $this->createForm(new MedicalProcedureTypeForm($em, $id), $procedureType);
    	$params = array('form' => $form->createView(), 'id' => $id);
    	return $this->render('AdminBundle:MedicalProcedureType:form.html.twig', $params);
    }

    public function saveAction()
    {
    	$request = $this->getRequest();
    	$id = $request->get('id', null);

    	if ('POST' == $request->getMethod()) {
    		$em = $this->getDoctrine()->getEntityManager();

			$procedureType = $id
				? $em->getRepository('MedicalProcedureBundle:MedicalProcedureType')->find($id) 
				: new MedicalProcedureType();

			$form = $this->createForm(new MedicalProcedureTypeForm($em), $procedureType);
    		$form->bind($request);

			if ($form->isValid()) {
				$procedureType = $form->getData();
				$em->persist($procedureType);
				$em->flush($procedureType);

    			$request->getSession()->setFlash('notice', 'New Procedure Type has been added!');
    			return $this->redirect($this->generateUrl('admin_procedureType_index'));
			} else {
		    	$params = array('form' => $form->createView(), 'id' => $id);
		    	return $this->render('AdminBundle:MedicalProcedureType:form.html.twig', $params);
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
