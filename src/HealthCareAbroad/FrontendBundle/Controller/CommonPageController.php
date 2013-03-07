<?php

namespace HealthCareAbroad\FrontendBundle\Controller;

use HealthCareAbroad\AdminBundle\Entity\Inquiry;

use HealthCareAbroad\FrontendBundle\Form\InquiryType;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class CommonPageController extends Controller
{
    public function viewPrivacyPolicyAction(){
        
        return $this->render('FrontendBundle:Static:privacyPolicy.html.twig');
    }
    
    public function viewTermsOfUseAction(){
    
        return $this->render('FrontendBundle:Static:termsOfUse.html.twig');
    }
    
    public function saveInquiryAction(Request $request)
    {
        $inquiry = new Inquiry();
        $form = $this->createForm(new InquiryType(), $inquiry);
        $inquirySubjects = $this->getDoctrine()->getRepository('AdminBundle:InquirySubject')->findAll();
        $error = false;
        $success = false;
        $errorArr = array();
        if($request->isMethod('POST')) {
            $form->bind($request);
            if($form->isValid()) {
                
                //get IP Address
                $remoteAddress = $this->getRequest()->getClientIp();
                
                $inquirySubject = $this->getDoctrine()->getRepository('AdminBundle:InquirySubject')->findOneByName($request->get('inquirySubject'));
                $inquiry->setInquirySubject($inquirySubject);
                $inquiry->setRemoteAddress($remoteAddress);
                $inquiry->setStatus(Inquiry::STATUS_ACTIVE);
                
                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($inquiry);
                $em->flush();
                $request->getSession()->setFlash('success', 'Your message has been sent! Thank you.');
                return $this->redirect($this->generateUrl('frontend_page_inquiry'));
            }else {
                $error = true;
            	
            	$form_errors = $this->get('validator')->validate($form);
            	foreach ($form_errors as $_err) {
            		$errorArr[] = $_err->getMessage();
            	}
            	
            }
        } 
        
        return $this->render('FrontendBundle:Static:inquiry.html.twig', 
                        array('form' => $form->createView(),
                              'inquirySubjects' => $inquirySubjects,
                              'isInquiry' => 1,
                              'error' => $error,
                        	  'error_list' => $errorArr));
    }
}