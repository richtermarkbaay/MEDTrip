<?php

namespace HealthCareAbroad\InstitutionBundle\Controller;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionTypes;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter;

use HealthCareAbroad\HelperBundle\Services\AlertRecipient;

use HealthCareAbroad\HelperBundle\Listener\Alerts\AlertTypes;

use HealthCareAbroad\HelperBundle\Services\AlertService;

use JMS\SecurityExtraBundle\Annotation\PreAuthorize;

use HealthCareAbroad\AdminBundle\Entity\ErrorReport;

use HealthCareAbroad\HelperBundle\Form\ErrorReportFormType;

use HealthCareAbroad\HelperBundle\Event\CreateErrorReportEvent;

use HealthCareAbroad\HelperBundle\Event\ErrorReportEvent;

use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Response;
use HealthCareAbroad\InstitutionBundle\Form\InstitutionFormType;
class DefaultController extends InstitutionAwareController
{
    public $institutionMedicalCenter;
    /**
     * @PreAuthorize("hasAnyRole('INSTITUTION_USER')")
     *
     */
    
    public function indexAction()
    {
        $institutionAlerts = $this->container->get('services.alert')->getAlertsByInstitution($this->institution);
        $newsRepository = $this->getDoctrine()->getRepository('HelperBundle:News');
        $news = $newsRepository->getLatestNews();
        
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
        ));
    }
    public function addClinicAction()
    {
        $institutionAlerts = $this->container->get('services.alert')->getAlertsByInstitution($this->institution);
        $newsRepository = $this->getDoctrine()->getRepository('HelperBundle:News');
        $news = $newsRepository->getLatestNews();
         
        $template = 'InstitutionBundle:Default:add.clinic.html.twig';
         
        return $this->render($template, array(
                        'alerts' => $institutionAlerts,
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

}
