<?php

namespace HealthCareAbroad\ProcedureBundle\Controller;


use HealthCareAbroad\ProcedureBundle\ProcedureBundle;

use HealthCareAbroad\HelperBundle\Entity\Tag;

use HealthCareAbroad\ProcedureBundle\Form\ProcedureType;

use HealthCareAbroad\ProcedureBundle\Form\DataTransformer\TagToObjectTransformer;

use HealthCareAbroad\ProcedureBundle\Entity\MedicalProcedure;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
		$criteria = array('status'=> 1);
		$procedures = $this->getDoctrine()->getEntityManager()->getRepository('ProcedureBundle:MedicalProcedure')->findBy($criteria);
    	$data = array('procedures'=>$procedures);
    	return $this->render('ProcedureBundle:Default:index.html.twig', $data);
    }

    public function addAction()
    {
    	$em = $this->getDoctrine()->getEntityManager();
    	$procedure = new MedicalProcedure();
    	$form = $this->createForm(new ProcedureType($em), $procedure);
    	$params = array('form' => $form->createView());
    	return $this->render('ProcedureBundle:Default:create.html.twig', $params);
    }
    
    public function editAction($id)
    {
    	$em = $this->getDoctrine()->getEntityManager();
    	$procedure = $em->getRepository('ProcedureBundle:MedicalProcedure')->find($id);
    	$form = $this->createForm(new ProcedureType($em), $procedure);
    	$params = array('form' => $form->createView(), 'id' => $procedure->getId());
    	return $this->render('ProcedureBundle:Default:create.html.twig', $params);
    }

    public function saveAction()
    {
    	$request = $this->getRequest();
    	
    	if ('POST' == $request->getMethod()) {    		
    		$em = $this->getDoctrine()->getEntityManager();

			$procedure = $request->get('id')
				? $em->getRepository('ProcedureBundle:MedicalProcedure')->find($request->get('id')) 
				: new MedicalProcedure();

			$form = $this->createForm(new ProcedureType($em), $procedure);
    		$form->bind($request);

    		// TODO - Validation Should be enabled!
    		//if ($form->isValid()) {
    			$procedure->setStatus(1);
    			$em->persist($procedure);
    			$em->flush($procedure);

    			$request->getSession()->setFlash('notice', 'New Procedure has been added!');
    			return $this->redirect($this->generateUrl('procedure_homepage'));
			//}
	
    	}
    }

    public function viewAction($id)
    {
    	$em = $this->getDoctrine()->getEntityManager();
    	$procedure = $em->getRepository('ProcedureBundle:MedicalProcedure')->find($id);

    	return $this->render('ProcedureBundle:Default:procedure.html.twig', array('procedure' => $procedure));
    }
    
    public function deleteAction($id)
    {
    	$em = $this->getDoctrine()->getEntityManager();
    	$procedure = $em->getRepository('ProcedureBundle:MedicalProcedure')->find($id);
    	$procedure->setStatus(0);
    	$em->persist($procedure);
    	$em->flush($procedure);
    	
    	$this->getRequest()->getSession()->setFlash('notice', 'Procedure has been deleted!');
    	return $this->redirect($this->generateUrl('procedure_homepage'));
    }
}
