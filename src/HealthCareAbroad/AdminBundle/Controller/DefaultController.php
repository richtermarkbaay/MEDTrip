<?php

namespace HealthCareAbroad\AdminBundle\Controller;

use HealthCareAbroad\HelperBundle\Services\AlertService;

use HealthCareAbroad\HelperBundle\Listener\Alerts\AlertTypes;

use Symfony\Component\HttpFoundation\Response;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

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
        $accountId = $this->getRequest()->getSession()->get('accountId');
//         $alertService = $this->container->get('services.alert');
//         $alerts = $alertService->getAdminAlerts($accountId);
//         $pendingListingAlerts = isset($alerts[AlertTypes::PENDING_LISTING]) ? $alerts[AlertTypes::PENDING_LISTING] : array();
//         $expiredListingAlerts = isset($alerts[AlertTypes::EXPIRED_LISTING]) ? $alerts[AlertTypes::EXPIRED_LISTING] : array();


//         $mailer = $this->get('mailer');
//         $message = \Swift_Message::newInstance()
//         ->setSubject('New Error Report')
//         ->setFrom('chris.velarde@chromedia.com')
//         ->setTo('chris.velarde@chromedia.com')
//         ->setBody('watatadsfsdf');
//         $sendResult = $mailer->send($message);
//         exit;
        $params = array(
//             'pendingListingAlerts' => $pendingListingAlerts,
//             'expiredListingAlerts' => $expiredListingAlerts,
                        'pendingListingAlerts' => array(),
                        'expiredListingAlerts' => array(),
        );

        return $this->render('AdminBundle:Default:index.html.twig', $params);
    }

    /**
     * @PreAuthorize("hasRole('ROLE_ADMIN')")
     */
    public function manageHcaDataAction()
    {
    	return $this->render('AdminBundle:Default:manageHcaDataDashboard.html.twig');
    }

    public function removeAlertAction($id, $rev)
    {
        $result = $this->get('services.alert')->delete($id, $rev);

        $response = new Response(json_encode($result));
        $response->headers->set('Content-Type', 'application/json');
        
        return $response;
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
		return $this->render('AdminBundle:Exception:error.html.twig', array(
				'form' => $form->createView(),
				'reportSubmitted' => true
		));
	}
    
    /**
     * Show edit history of an object
     * Required REQUEST parameters are:
     *     objectId - int
     *     objectClass - base64_encoded fully qualified class name
     * 
     * @param Request $request
     * @return \HealthCareAbroad\AdminBundle\Controller\Response
     * @author Allejo Chris G. Velarde
     */
    public function showEditHistoryAction(Request $request)
    {
        if (!$request->isXmlHttpRequest()) {
            //throw $this->createNotFoundException("Only supports AJAX request");
        }
        $objectId = $request->get('objectId', null);
        $objectClass = $request->get('objectClass', null);
        if ($objectId === null || $objectClass === null) {
            return new Response("objectId and objectClass are required parameters", 400);
        }
        
        $objectClass = \base64_decode($objectClass);
        if (!\class_exists($objectClass)) {
            throw $this->createNotFoundException("Cannot view history of invalid class {$objectClass}");
        }
        
        $object = $this->getDoctrine()->getRepository($objectClass)->find($objectId);
        if (!$object) {
            throw $this->createNotFoundException("Object #{$objectId} of class {$objectClass} does not exist.");
        }
        
        $service = $this->get('services.log.entity_version');
        $versionEntries = $service->getObjectVersionEntries($object);
        
        $template = 'AdminBundle:Default:editHistory.html.twig';
        if ($request->isXmlHttpRequest()) {
            $template = 'AdminBundle:Default:versionsList.html.twig';
        }
        
        $objectName = $object->__toString();
        return $this->render($template, array(
            'versions' => $versionEntries,
            'objectName' => $objectName
        ));
    }
}
