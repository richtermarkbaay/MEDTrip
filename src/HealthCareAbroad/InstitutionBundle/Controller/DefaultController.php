<?php

namespace HealthCareAbroad\InstitutionBundle\Controller;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionInquiry;

use HealthCareAbroad\InstitutionBundle\Entity\Institution;

use HealthCareAbroad\InstitutionBundle\Form\InstitutionProfileFormType;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenterStatus;

use HealthCareAbroad\PagerBundle\Adapter\DoctrineOrmAdapter;

use HealthCareAbroad\PagerBundle\Pager;

use HealthCareAbroad\AdminBundle\Entity\ErrorReport;
use HealthCareAbroad\HelperBundle\Form\ErrorReportFormType;
use HealthCareAbroad\HelperBundle\Event\CreateErrorReportEvent;
use HealthCareAbroad\HelperBundle\Event\ErrorReportEvent;
use HealthCareAbroad\InstitutionBundle\Entity\InstitutionTypes;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use JMS\SecurityExtraBundle\Annotation\PreAuthorize;

class DefaultController extends InstitutionAwareController
{
    /**
     * @var InstitutionMedicalCenterRepository
     */
    private $repository;
    
    /**
     * @var InstitutionMedicalCenterService
     */
    private $service;
    
    public $institutionMedicalCenter;
    
    
    public function indexAction(Request $request)
    {
        //$institutionAlerts = $this->container->get('services.alert')->getAlertsByInstitution($this->institution);
        $institutionAlerts = array();
        
        if($request->server->has('HTTP_REFERER')){
            if (\preg_match('/setup-doctors/i', $request->server->get('HTTP_REFERER'))) {
                $signup = true;
            }
        }else{
            $signup = false;
        }
        
        // TODO - Deprecated??
        //$newsRepository = $this->getDoctrine()->getRepository('HelperBundle:News');
        //$news = $newsRepository->getLatestNews();
        $news = array();

        if (InstitutionTypes::MULTIPLE_CENTER == $this->institution->getType()) {
            $template = 'InstitutionBundle:Default:dashboard.multipleCenter.html.twig';
        }
        else {
            $template = 'InstitutionBundle:Default:dashboard.singleCenter.html.twig';
        }
    
        return $this->render($template, array(
                        'alerts' => $institutionAlerts,
                        'news' => $news,
                        'institution' => $this->institution,
                        'isDashBoard' => true,
                        'signup' => $signup
                        
        ));
    }
    
    public function viewAllInquiriesAction(Request $request)
    {
        $tab = $request->get('tabName','all');
        $template = "InstitutionBundle:Inquiry:inquiries.html.twig";
        $inquiryArr = $this->get('services.institution')->getInstitutionInquiriesBySelectedTab($this->institution, $tab);
        
        return $this->render($template, array(
                        'institution' => $this->institution,
                        'inquiries' => \json_encode($inquiryArr, JSON_HEX_APOS),
                        'isInquiry' => true,
                        'tabName' => $tab
        ));
    }
    
    public function viewInquiryAction(Request $request)
    {
        $inquiryId = $request->get('id');
        $inquiry = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionInquiry')->findOneById($inquiryId);
        $this->get('services.institution')->setInstitutionInquiryStatus($inquiry, InstitutionInquiry::STATUS_READ);
        
        return $this->render('InstitutionBundle:Inquiry:view_inquiry.html.twig', array(
                        'inquiry' => $inquiry,
                        'isInquiry' => true,
                        'prevPath' => $this->getRequest()->headers->get('referer')
        ));
    }
    
    public function removeInquiryAction(Request $request)
    {
        $institutionService = $this->get('services.institution');
        $inquiryId = $request->get('id');
        $tab = $request->get('tabName');
        $inquiry = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionInquiry')->findOneById($inquiryId);
        $institutionService->setInstitutionInquiryStatus($inquiry, InstitutionInquiry::STATUS_DELETED);
        $inquiryArr = $institutionService->getInstitutionInquiriesBySelectedTab($this->institution, $tab);
        $output = array('inquiryList' => $inquiryArr,
                        'readCntr' => $institutionService->getInstitutionInquiriesByStatus($this->institution, InstitutionInquiry::STATUS_READ),
                        'unreadCntr' => $institutionService->getInstitutionInquiriesByStatus($this->institution, InstitutionInquiry::STATUS_UNREAD));
        $response = new Response(\json_encode($output, JSON_HEX_APOS),200, array('content-type' => 'application/json'));
        
        return $response;
        
    }
    
    public function ajaxSetInstitutionInquiryStatusAction(Request $request)
    {
        $institutionService = $this->get('services.institution');
        $inquiryList = $request->get('inquiryListArr');
        $inquiryStatus = InstitutionInquiry::STATUS_READ;
        $tab = $request->get('tabName');
        if($request->get('status') == '1') {
            $inquiryStatus = InstitutionInquiry::STATUS_UNREAD;
        }
        $inquiries = $institutionService->setInstitutionInquiryListStatus($inquiryList, $inquiryStatus);
        $inquiryArr = $institutionService->getInstitutionInquiriesBySelectedTab($this->institution, $tab);
        $output = array('inquiryList' => $inquiryArr,
                        'readCntr' => $institutionService->getInstitutionInquiriesByStatus($this->institution, InstitutionInquiry::STATUS_READ),
                        'unreadCntr' => $institutionService->getInstitutionInquiriesByStatus($this->institution, InstitutionInquiry::STATUS_UNREAD));
        $response = new Response(\json_encode($output, JSON_HEX_APOS),200, array('content-type' => 'application/json'));
        
        return $response;
    }
    
    public function addClinicAction()
    {
        //$institutionAlerts = $this->container->get('services.alert')->getAlertsByInstitution($this->institution);
        $newsRepository = $this->getDoctrine()->getRepository('HelperBundle:News');
        $news = $newsRepository->getLatestNews();
         
        $template = 'InstitutionBundle:Default:add.clinic.html.twig';
         
        return $this->render($template, array(
                        'alerts' => array(),
                        'news' => $news,
                        'institution' => $this->institution,
        ));
    }
    public function error403Action()
    {
        return $this->render('InstitutionBundle:Exception:error403.html.twig');
    }
    
    /**
     * Add Error Report
     * @author Chaztine Blance
     */
    public function errorReportAction()
    {
    	$request = $this->getRequest();
    	$em = $this->getDoctrine()->getEntityManager();
    	$userId = $this->container->get('session')->get('accountId');
    	     	
    	if($userId){
    		
    		$errorReport = new ErrorReport();
    		$form = $this->createForm(new ErrorReportFormType(), $errorReport);   		
    		$form->bind($request);

    		if ($form->isValid()) {	

		    	$errorReport->setLoggedUserId($userId);
 			    $errorReport->setStatus(1);
 			    $em->persist($errorReport);
 			    $em->flush($errorReport);
 			    
 			    //// create event on sendEmail and dispatch
 			    $event = new CreateErrorReportEvent($errorReport);
           	   	$sendResult = $this->get('event_dispatcher')->dispatch(ErrorReportEvent::ON_CREATE_REPORT, $event);
	    	    	
           	   	if ($sendResult) {         	   		 
           	   		$this->get('session')->setFlash('success', "Successfully sent error report to HealthCareAbroad");		
           	   	}
           	   	else {
           	   		$this->get('session')->setFlash('error', "Failed to send Report to HealthCareAbroad");
           	   	}           	   				  
		    }
		}
		return $this->render('InstitutionBundle:Exception:error.html.twig', array(
				'form' => $form->createView(),
				'reportSubmitted' => true
		));
	}
	
	public function ajaxSendErrorReportAction(Request $request){
	
	    if('POST' != $request->getMethod()) {
	        return new Response("Save requires POST method!", 405);
	    }
	    $errorReport = new ErrorReport();
	    $form = $this->createForm(New ErrorReportFormType(), $errorReport);
	    $form->bind($request);
	
	    if ($form->isValid()) {
	        $em = $this->getDoctrine()->getEntityManager();
	        $errorReport->setLoggedUserId(0);
	        $errorReport->setStatus(ErrorReport::STATUS_ACTIVE);
	        $errorReport->setFlag(ErrorReport::COMMON_REPORT);
	        $em->persist($errorReport);
	        $em->flush();
	
	        return new Response(\json_encode(true),200, array('content-type' => 'application/json'));
	    }
	    else {
	        $errors = array();
	        $form_errors = $this->get('validator')->validate($form);
	
	        foreach ($form_errors as $_err) {
	            $errors[] = array('field' => str_replace('data.','',$_err->getPropertyPath()), 'error' => $_err->getMessage());
	        }
	
	        $captchaError = $form->get('captcha')->getErrors();
	        if(count($captchaError)) {
	            $errors[] = array('field' => $form->get('captcha')->getName(), 'error' => $captchaError[0]->getMessageTemplate());
	        }
	        $response = new Response(\json_encode(array('html' => $errors)), 400, array('content-type' => 'application/json'));
	    }
	
	    return $response;
	}
	
	public function mediaAjaxDelete()
	{
	    
	}
}
