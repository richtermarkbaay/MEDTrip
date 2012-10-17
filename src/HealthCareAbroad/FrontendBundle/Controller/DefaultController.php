<?php

namespace HealthCareAbroad\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use HealthCareAbroad\AdminBundle\Entity\ErrorReport;

use HealthCareAbroad\FrontendBundle\Entity\NewsletterSubscriber;

use HealthCareAbroad\HelperBundle\Form\ErrorReportFormType;

use HealthCareAbroad\FrontendBundle\Form\NewsletterSubscriberFormType;

use HealthCareAbroad\HelperBundle\Event\CreateErrorReportEvent;

use HealthCareAbroad\HelperBundle\Event\ErrorReportEvent;

use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    public function indexAction()
    {
       $form = $this->createForm(New NewsletterSubscriberFormType(), new NewsletterSubscriber());
    	    	
        return $this->render('::splash.frontend.html.twig', array(
        		'form' => $form->createView(),
        ));
    }
    
    /*
     * Newsletter subscribe
     * @author Chaztine Blance
     */
    public function newAction()
    {
    	$ipAddress = $this->getRequest()->getClientIp();
    	
    	$request = $this->getRequest();
    	$em = $this->getDoctrine()->getEntityManager();
    	
    	$newsletterSubscriber = new NewsletterSubscriber();
    	$form = $this->createForm(new NewsletterSubscriberFormType(), $newsletterSubscriber);

    	if ($request->isMethod('POST')) {
    		$form->bind($request);
    	
    		if ($form->isValid()) {
    	
    			try {
    				  	$newsletterSubscriber->setIpAddress($ipAddress);
			    		$em->persist($newsletterSubscriber);
			    		$em->flush($newsletterSubscriber);
				    	
			    		$this->get('session')->setFlash('success', "Successfully Subscribe to HealthCareAbroad");
    			}
    			catch (\Exception $e) {
    				$request->getSession()->setFlash("error", "Failed to save advertisement due to unexpected error.");
    				$redirectUrl = $this->generateUrl("main_homepage");
    			}
    		}
    	}
    	return $this->render('::splash.frontend.html.twig', array(
    					 
    					'form' => $form->createView(),
    	));
   
    }

    /**************************************************************************
     Dynamic Routes

     If a name change or a different controller is required modify
     FrontendBundle\Services\FrontendRouteService::extrapolateControllerFromVariables($variables)
     **************************************************************************/
    public function commonLandingAction()
    {
        var_dump($this->getRequest()->attributes->get('_route_params'));
        echo "adi na ha landing action"; exit;
        //return $this->render('FrontendBundle:Default:index.html.twig', array());
    }

    public function listCentersForCountryAction()
    {
        var_dump($this->getRequest()->attributes->get('_route_params'));
        exit;
    }

    public function listCentersForCityAction()
    {
        var_dump($this->getRequest()->attributes->get('_route_params'));
        exit;
    }

    public function listTreatmentsForCenterAction()
    {
        var_dump($this->getRequest()->attributes->get('_route_params'));
        exit;
    }

    public function clinicProfileAction()
    {
        var_dump($this->getRequest()->attributes->get('_route_params'));
        exit;
    }

    public function clinicProfileWithTreatmentAction()
    {
        var_dump($this->getRequest()->attributes->get('_route_params'));
        exit;
    }

    /**************************************************************************
     END Dynamic Routes
    **************************************************************************/

    /**
     * Add Error Report
     *
     * @author Chaztine Blance
     */
    public function errorReportAction()
    {

        $request = $this->getRequest();
        $em = $this->getDoctrine()->getEntityManager();

            $errorReport = new ErrorReport();
            $form = $this->createForm(new ErrorReportFormType(), $errorReport);
            $form->bind($request);

            if ($form->isValid()) {

                $errorReport->setLoggedUserId(0);
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

        return $this->render('TwigBundle:Exception:error.html.twig', array(
                        'form' => $form->createView(),
                        'reportSubmitted' => true
        ));
    }
}
