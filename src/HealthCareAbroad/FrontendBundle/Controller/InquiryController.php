<?php

namespace HealthCareAbroad\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use HealthCareAbroad\FrontendBundle\Form\InquiryType;
use HealthCareAbroad\AdminBundle\Entity\Inquiry;
use HealthCareAbroad\UserBundle\Entity\SiteUser;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
class InquiryController extends Controller
{
    public function indexAction()
    {
    	$form = $this->createForm(new InquiryType());
    	
    	if ($this->getRequest()->isMethod('POST')) {
    	
    		$form->bindRequest($this->getRequest());
    	
    		if ($form->isValid()) {
    			 
    			//create inquire
    			$inquire = new Inquiry();
    			$inquire->setFirstName($form->get('firstName')->getData());
    			$inquire->setLastName($form->get('lastName')->getData());
    			$inquire->setEmail($form->get('email')->getData());
    			$inquire->setInquirySubject($form->get('inquiry_subject')->getData());
    			$inquire->setMessage($form->get('message')->getData());
    			$inquire->setStatus(SiteUser::STATUS_ACTIVE);
    			$inquire = $this->get('services.inquire')->createInquiry($inquire);
    			
    			if ( count($inquire) > 0 ) {
    				$this->get('session')->setFlash('notice', "Successfully submitted.");
    			}
    			else
    			{
    				$this->get('session')->setFlash('notice', "Unable to send inqueries!");
    					
    			}
    		}
    	}
    	
        return $this->render('FrontendBundle:Inquiry:index.html.twig', array(
        		'form' => $form->createView(),
        ));
    }
}
