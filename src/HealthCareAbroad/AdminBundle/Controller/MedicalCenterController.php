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
		$medicalCenters = $this->getDoctrine()->getEntityManager()->getRepository('MedicalProcedureBundle:MedicalCenter')->findAll();
    	
    	return $this->render('AdminBundle:MedicalCenter:index.html.twig', array('medicalCenters' => $medicalCenters));
    }

    public function addAction()
    {
    	$form = $this->createForm(new MedicalCenterType(), new MedicalCenter());

    	return $this->render('AdminBundle:MedicalCenter:form.html.twig', array('form' => $form->createView()));
    }
    
    public function editAction($id)
    {
    	$medicalCenter = $this->getDoctrine()->getEntityManager()->getRepository('MedicalProcedureBundle:MedicalCenter')->find($id);
    	
    	$form = $this->createForm(new MedicalCenterType(), $medicalCenter);
    	
    	return $this->render('AdminBundle:MedicalCenter:form.html.twig', array('form' => $form->createView()));
    }

    public function saveAction()
    {
    	$request = $this->getRequest();

    	if ('POST' == $request->getMethod()) {
    		$em = $this->getDoctrine()->getEntityManager();

			$medicalCenter = $request->get('id')
				? $em->getRepository('MedicalProcedureBundle:MedicalCenter')->find($request->get('id')) 
				: new MedicalCenter();

			$form = $this->createForm(new MedicalCenterType(), $medicalCenter);
    		$form->bind($request);

    		if ($form->isValid()) {
    			$medicalCenter->setStatus(1);
    			$em->persist($medicalCenter);
    			$em->flush($medicalCenter);

    			$msg = $request->get('id') 
    				? '"' .$medicalCenter->getName() . '" has been updated!' 
    				: 'New medica center has been added!'; 
    			$request->getSession()->setFlash('notice', $msg);
    			
    			return $this->redirect($this->generateUrl('admin_medicalCenter_index'));
    			
			} else {
				
				return $this->redirect($this->generateUrl('admin_medicalCenter_add'));
				
			}
    	}
    }
    
    public function updateStatusAction($id)
    {
    	$result = false;
    	$em = $this->getDoctrine()->getEntityManager();
		$tag = $em->getRepository('HelperBundle:Tag')->find($id);

		if($tag) {
			$status = $tag->getStatus() == Tag::STATUS_ACTIVE ? Tag::STATUS_INACTIVE : Tag::STATUS_ACTIVE;
			$tag->setStatus($status);
			$em->persist($tag);
			$em->flush($tag);
			$result = true;
		}

		$response = new Response(json_encode($result));
		$response->headers->set('Content-Type', 'application/json');
		
		return $response;
    }
}