<?php
namespace HealthCareAbroad\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use HealthCareAbroad\MedicalProcedureBundle\Entity\MedicalCenter;
use HealthCareAbroad\MedicalProcedureBundle\Form\MedicalCenterType;
use JMS\SecurityExtraBundle\Annotation\PreAuthorize;

class MedicalCenterController extends Controller
{
    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_VIEW_MEDICAL_CENTERS')")
     */
    public function indexAction()
    {
    	return $this->render('AdminBundle:MedicalCenter:index.html.twig', array('medicalCenters' => $this->filteredResult));
    }

    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_MEDICAL_CENTER')")
     */
    public function addAction()
    {
    	$form = $this->createForm(new MedicalCenterType(), new MedicalCenter());

    	return $this->render('AdminBundle:MedicalCenter:form.html.twig', array(
    			'id' => null,
    			'form' => $form->createView(),  
    			'formAction' => $this->generateUrl('admin_medicalCenter_create')
    	));
    }
    
    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_MEDICAL_CENTER')")
     */
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
    
    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_MEDICAL_CENTER')")
     */
    public function saveAction()
    {
    	$request = $this->getRequest();	
    	if('POST' != $request->getMethod()) {
    		return new Response("Save requires POST method!", 405);
    	}

    	$id = $request->get('id', null);
    	$em = $this->getDoctrine()->getEntityManager();

		$medicalCenter = $id
				? $em->getRepository('MedicalProcedureBundle:MedicalCenter')->find($id) 
				: new MedicalCenter();

		$form = $this->createForm(new MedicalCenterType(), $medicalCenter);
   		$form->bind($request);

   		if ($form->isValid()) {
   			$em->persist($medicalCenter);
   			$em->flush($medicalCenter);

   			$request->getSession()->setFlash('success', 'Medical center saved!');

   			if($request->get('add-another-center'))
   				return $this->redirect($this->generateUrl('admin_medicalCenter_add'));
   			else 
	   			return $this->redirect($this->generateUrl('admin_medicalCenter_edit', array('id' => $medicalCenter->getId())));
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
    
    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_DELETE_MANAGE_MEDICAL_CENTER')")
     */
    public function updateStatusAction($id)
    {
    	$result = false;
    	$em = $this->getDoctrine()->getEntityManager();
    	$medicalCenter = $em->getRepository('MedicalProcedureBundle:MedicalCenter')->find($id);

		if ($medicalCenter) {
			$medicalCenter->setStatus($medicalCenter->getStatus() ? MedicalCenter::STATUS_INACTIVE : MedicalCenter::STATUS_ACTIVE);
			$em->persist($medicalCenter);
			$em->flush($medicalCenter);
			$result = true;
		}

		$response = new Response(json_encode($result));
		$response->headers->set('Content-Type', 'application/json');

		return $response;
    }
    
}