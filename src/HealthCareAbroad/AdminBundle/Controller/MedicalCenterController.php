<?php
namespace HealthCareAbroad\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

use HealthCareAbroad\MedicalProcedureBundle\Entity\MedicalCenter;
use HealthCareAbroad\MedicalProcedureBundle\Form\MedicalCenterType;

class MedicalCenterController extends Controller
{
    public function indexAction()
    {
		$medicalCenters = $this->getDoctrine()->getEntityManager()
				->getRepository('MedicalProcedureBundle:MedicalCenter')->findAll();
    	
    	return $this->render('AdminBundle:MedicalCenter:index.html.twig', array(
    			'medicalCenters' => $medicalCenters
    	));
    }

    public function addAction()
    {
    	$form = $this->createForm(new MedicalCenterType(), new MedicalCenter());

    	return $this->render('AdminBundle:MedicalCenter:form.html.twig', array(
    			'id' => null,
    			'form' => $form->createView(),  
    			'formAction' => $this->generateUrl('admin_medicalCenter_create')
    	));
    }
    
    public function editAction($id)
    {
    	$medicalCenter = $this->getDoctrine()->getEntityManager()
    			->getRepository('MedicalProcedureBundle:MedicalCenter')->find($id);
    	
    	$form = $this->createForm(new MedicalCenterType(), $medicalCenter);

    	return $this->render('AdminBundle:MedicalCenter:form.html.twig', array(
    			'id' => $id,
    			'form' => $form->createView(), 
    			'formAction' => $this->generateUrl('admin_medicalCenter_update', array('id' => $id))
    	));
    }
    
    public function saveAction()
    {
    	$id = $this->getRequest()->get('id', null);
    	$em = $this->getDoctrine()->getEntityManager();

		$medicalCenter = $id
				? $em->getRepository('MedicalProcedureBundle:MedicalCenter')->find($id) 
				: new MedicalCenter();

		$form = $this->createForm(new MedicalCenterType(), $medicalCenter);
   		$form->bind($this->getRequest());

   		if ($form->isValid()) {
   			$em->persist($medicalCenter);
   			$em->flush($medicalCenter);
   			
   			$this->getRequest()->getSession()->setFlash('notice', 'Medical center saved!');
    			
   			return $this->redirect($this->generateUrl('admin_medicalCenter_index'));
		}

		$formAction = $id 
			? $this->generateUrl('admin_medicalCenter_update', array('id' => $id))
			: $this->generateUrl('admin_medicalCenter_create');
		
		return $this->render('AdminBundle:MedicalCenter:form.html.twig', array(
				'id' => $id,
				'form' => $form->createView(),
				'formAction' => $formAction 
		));				
    }
    
    public function updateStatusAction()
    {
    	$request = $this->getRequest();
    	$em = $this->getDoctrine()->getEntityManager();
    	$medicalCenter = $em->getRepository('MedicalProcedureBundle:MedicalCenter')->find($request->get('id'));

		if ($medicalCenter) {
			$medicalCenter->setStatus($request->get('status'));
			$em->persist($medicalCenter);
			$em->flush($medicalCenter);
		}

		return $this->redirect($this->generateUrl('admin_medicalCenter_index'));
    }
    
    public function searchMedicalCentersAction($term)
    {
    	$data = $this->getDoctrine()->getEntityManager()->getRepository('MedicalProcedureBundle:MedicalCenter')->searchMedicalCenters($term);

    	$response = new Response(json_encode($data));
    	$response->headers->set('Content-Type', 'application/json');
    
    	return $response;
    }
}