<?php

namespace HealthCareAbroad\FrontendBundle\Controller;

use HealthCareAbroad\FrontendBundle\Form\InstitutionInquiryFormType;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionInquiry;

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
    
    public function ajaxSaveInquiryAction(Request $request)
    {
        $institutionInquiry = new InstitutionInquiry();
        $form = $this->createForm(new InstitutionInquiryFormType(), $institutionInquiry);
    
        $form->bindRequest($request);
        if ($form->isValid()) {
            if($request->get('imcId')) {
                $imc = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionMedicalCenter')->find($request->get('imcId'));
                $institution = $imc->getInstitution();
                $institutionInquiry->setInstitutionMedicalCenter($imc);
            }
            else {
                $institution = $this->getDoctrine()->getRepository('InstitutionBundle:Institution')->find($request->get('institutionId'));
            }
            $institutionInquiry->setInstitution($institution);
            $institutionInquiry->setStatus(InstitutionInquiry::STATUS_SAVE);
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($institutionInquiry);
            $em->flush();
    
            $this->get('session')->setFlash('notice', "Successfully saved!");
            $response = new Response(\json_encode(array('id' => $institutionInquiry->getId())), 200, array('content-type' => 'application/json'));
        }
        else {
            $errors = array();
            $form_errors = $this->get('validator')->validate($form);
            foreach ($form_errors as $_err) {
                $errors[] = array('field' => str_replace('data.','',$_err->getPropertyPath()), 'error' => $_err->getMessage());
            }
            $response = new Response(\json_encode(array('html' => $errors)), 400, array('content-type' => 'application/json'));
        }
    
        return $response;
    }
}
