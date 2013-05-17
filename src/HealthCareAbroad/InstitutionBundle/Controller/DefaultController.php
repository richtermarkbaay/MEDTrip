<?php

namespace HealthCareAbroad\InstitutionBundle\Controller;

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
    
    
    public function indexAction()
    {
        //$institutionAlerts = $this->container->get('services.alert')->getAlertsByInstitution($this->institution);
        $institutionAlerts = array();
    
        
        
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
                        'isDashBoard' => true
        ));
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
