<?php

namespace HealthCareAbroad\InstitutionBundle\Controller;
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

class DefaultController extends InstitutionAwareController
{
    /**
     * @PreAuthorize("hasAnyRole('INSTITUTION_USER')")
     *
     */
    public function indexAction()
    {
        $institutionAlerts = $this->container->get('services.alert')->getAlertsByInstitution($this->institution);
//         $draftAlerts = isset($institutionAlerts[AlertTypes::DRAFT_LISTING]) ? $institutionAlerts[AlertTypes::DRAFT_LISTING] : array();
//         $expiredListingAlerts = isset($institutionAlerts[AlertTypes::EXPIRED_LISTING]) ? $institutionAlerts[AlertTypes::EXPIRED_LISTING] : array();
//         $approvedListingAlerts = isset($institutionAlerts[AlertTypes::APPROVED_LISTING]) ? $institutionAlerts[AlertTypes::APPROVED_LISTING] : array();

        $newsRepository = $this->getDoctrine()->getRepository('HelperBundle:News');
        $news = $newsRepository->getLatestNews();

        return $this->render('InstitutionBundle:Default:index.html.twig', array(
            'alerts' => $institutionAlerts,
    		'news' => $news,
        ));
    }

    public function error403Action()
    {
        return $this->render('InstitutionBundle:Exception:error403.html.twig');
    }
    
    /**
     * Add Error Report
     *
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
