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
use HealthCareAbroad\InstitutionBundle\Entity\InstitutionSignupStepStatus;

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
    /**
     * @PreAuthorize("hasAnyRole('INSTITUTION_USER')")
     *
     */
    
    public function preExecute()
    {
        $this->repository = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionMedicalCenter');
        $this->service = $this->get('services.institution_medical_center');
    }
    public function indexAction(Request $request)
    {
        //$institutionAlerts = $this->container->get('services.alert')->getAlertsByInstitution($this->institution);
        $institutionAlerts = array();
        
        // TODO - Deprecated?? 
        //$newsRepository = $this->getDoctrine()->getRepository('HelperBundle:News');
        //$news = $newsRepository->getLatestNews();
        $isSingleCenter = false;

        $signupStepStatus = $this->institution->getSignupStepStatus();
        
        if(!InstitutionSignupStepStatus::hasCompletedSteps($signupStepStatus)) {
            $params = array();
            $routeName = InstitutionSignupStepStatus::getRouteNameByStatus($signupStepStatus);
            if(!InstitutionSignupStepStatus::isStep1($signupStepStatus)) {
                if(!$this->institutionMedicalCenter) {
                    $this->institutionMedicalCenter = $this->get('services.institution')->getFirstMedicalCenter($this->institution);
                }
                $params['imcId'] = $this->institutionMedicalCenter->getId();
            }
            
            return $this->redirect($this->generateUrl($routeName, $params));
        }
        $form = $this->createForm(new InstitutionProfileFormType(), $this->institution);
        if (InstitutionTypes::MULTIPLE_CENTER == $this->institution->getType()) {
            
            $approvedCenters = $this->repository->getInstitutionMedicalCentersByStatusQueryBuilder($this->institution, InstitutionMedicalCenterStatus::APPROVED);
            $pagerAdapter = new DoctrineOrmAdapter($approvedCenters);
            $pagerParams = array(
                            'page' => $request->get('page', 1),
                            'limit' => 10
            );
            $pager = new Pager($pagerAdapter, $pagerParams);
            
            $template = 'InstitutionBundle:Default:dashboard.multipleCenter.html.twig';
            $params =  array(
                    'institution' => $this->institution,
                    'isDashBoard' => true,
                    'isSingleCenter' => $isSingleCenter,
                    'statusList' => InstitutionMedicalCenterStatus::getStatusList(),
                    'pager' => $pager,
                    'medicalCenters' => $pager->getResults(),
                    'institutionForm' => $form->createView(),
                );
        }
        else {
            $isSingleCenter = true;
            $template = 'InstitutionBundle:Default:dashboard.singleCenter.html.twig';
            
            $params =  array(
                'institution' => $this->institution,
                'isDashBoard' => true,
                'isSingleCenter' => $isSingleCenter
            );
        }
        
        return $this->render($template, $params);
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
