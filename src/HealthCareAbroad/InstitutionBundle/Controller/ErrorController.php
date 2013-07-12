<?php
namespace HealthCareAbroad\InstitutionBundle\Controller;

use HealthCareAbroad\AdminBundle\Entity\ErrorReport;

use HealthCareAbroad\HelperBundle\Form\ErrorReportFormType;
use HealthCareAbroad\HelperBundle\Event\CreateErrorReportEvent;
use HealthCareAbroad\HelperBundle\Event\ErrorReportEvent;

use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class ErrorController extends InstitutionAwareController
{
 
    public function error403Action()
    {
        throw new AccessDeniedHttpException();
    }
    
    public function error401Action()
    {
        throw new HttpException(401);
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

	    } else {
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
	
	public function error500Action()
	{
	    throw new \Exception('Something went wrong!');
	}
}