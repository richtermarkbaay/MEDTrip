<?php

namespace HealthCareAbroad\AdminBundle\Controller;

use HealthCareAbroad\AdvertisementBundle\Entity\AdvertisementType;

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
        
        $advertisementTypes = $this->getDoctrine()->getEntityManager()->getRepository('AdvertisementBundle:AdvertisementType')->findByStatus(AdvertisementType::STATUS_ACTIVE);
        
        $accountId = $this->getRequest()->getSession()->get('accountId');
        //$alerts = $this->container->get('services.alert')->getAdminAlerts($accountId);
        $alerts = array();
        
        $adsTypeObj = array();
        foreach($advertisementTypes as $adsType)
        {
            $adsTypeObj[] = array('name' => $adsType->getName(), 'url' => $this->generateUrl('admin_advertisement_index',array('advertisementType' => $adsType->getId(), 'status' => 'all')), 'label' => $adsType->getName(), 'icon' => 'icon-list');
        }
        
        //$pendingListingAlerts = isset($alerts[AlertTypes::PENDING_LISTING]) ? $alerts[AlertTypes::PENDING_LISTING] : array();
        //$expiredListingAlerts = isset($alerts[AlertTypes::EXPIRED_LISTING]) ? $alerts[AlertTypes::EXPIRED_LISTING] : array();

        $params = array(
            'alerts' => $alerts,
            'adsTypeObj' => \json_encode($adsTypeObj)
            
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
     * This is a global/generic ADMIN delete media function.
     * Please use this function instead of creating another.
     *
     * @author Adelbert D. Silla
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function ajaxDeleteAction(Request $request)
    {
        $result = false;

        $media = $this->getDoctrine()->getRepository('MediaBundle:Media')->find($request->get('media_id'));
 
        $this->get($request->get('service_id'))->delete($media, $request->get('imageType'));

        $response = new Response(json_encode(true));
        $response->headers->set('Content-Type', 'application/json');
    
        return $response;
    }
    
    public function flushCacheAction()
    {
        $this->get('services.memcache')->flush();
        
        return new Response("Memcache Flushed", 200);
    }

}
