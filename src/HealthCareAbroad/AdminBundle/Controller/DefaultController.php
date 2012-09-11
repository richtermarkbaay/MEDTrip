<?php

namespace HealthCareAbroad\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\HttpFoundation\Request;

use JMS\SecurityExtraBundle\Annotation\PreAuthorize;

use HealthCareAbroad\AdminBundle\Entity\ErrorReport;

use HealthCareAbroad\HelperBundle\Form\ErrorReportFormType;

use HealthCareAbroad\HelperBundle\Event\CreateErrorReportEvent;

use HealthCareAbroad\HelperBundle\Event\ErrorReportEvent;

class DefaultController extends Controller
{
    /**
     * @PreAuthorize("hasRole('ROLE_ADMIN')")
     */
    public function indexAction()
    {
        return $this->render('AdminBundle:Default:index.html.twig');
    }

    /**
     * @PreAuthorize("hasRole('ROLE_ADMIN')")
     */
    public function manageHcaDataAction()
    {
    	return $this->render('AdminBundle:Default:manageHcaDataDashboard.html.twig');
    }

    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN')")
     */
    public function settingsAction()
    {
        return $this->render('AdminBundle:Default:settings.html.twig');
    }
    
    public function error403Action()
    {
        return $this->render('AdminBundle:Exception:error403.html.twig');
    }

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
		return $this->render('AdminBundle:Exception:error.html.twig', array(
				'form' => $form->createView(),
				'reportSubmitted' => true
		));
	}
}
