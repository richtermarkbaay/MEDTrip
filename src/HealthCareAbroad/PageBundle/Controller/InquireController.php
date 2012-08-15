<?php

namespace HealthCareAbroad\PageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use HealthCareAbroad\PageBundle\Form\InquireType;
use HealthCareAbroad\AdminBundle\Entity\Inquire;
use HealthCareAbroad\UserBundle\Entity\SiteUser;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;

class InquireController extends Controller
{
    public function indexAction()
    {
    	$form = $this->createForm(new InquireType());
    	
    	if ($this->getRequest()->isMethod('POST')) {
    	
    		$form->bindRequest($this->getRequest());
    	
    		if ($form->isValid()) {
    			 
    			//create inquire
    			$inquire = new Inquire();
    			$inquire->setFirstName($form->get('firstName')->getData());
    			$inquire->setLastName($form->get('lastName')->getData());
    			$inquire->setEmail($form->get('email')->getData());
    			$inquire->setInquireAbout($form->get('inquire_about')->getData());
    			$inquire->setMessage($form->get('message')->getData());
    			$inquire->setStatus(SiteUser::STATUS_ACTIVE);
    			$inquire = $this->get('services.inquire')->createInquire($inquire);
    			
    			if ( count($inquire) > 0 ) {
    				$this->get('session')->setFlash('notice', "Successfully submitted.");
    			}
    			else
    			{
    				$this->get('session')->setFlash('notice', "Unable to send inqueries!");
    					
    			}
    		}
    	}
    	
        return $this->render('PageBundle:Inquire:index.html.twig', array(
        		'form' => $form->createView(),
        ));
    }
}
