<?php

namespace HealthCareAbroad\FrontendBundle\Controller;

use HealthCareAbroad\InstitutionBundle\Event\InstitutionBundleEvents;
use HealthCareAbroad\InstitutionBundle\Event\InstitutionEvent;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use HealthCareAbroad\HelperBundle\Form\FeedbackMessageFormType;
use HealthCareAbroad\HelperBundle\Entity\FeedbackMessage;

class FeedbackController extends Controller
{
//     public function viewAction()
//     {
//         $form = $this->createForm(New FeedbackMessageFormType(), new FeedbackMessage());
        
//         return $this->render('FrontendBundle:Embed:modal.feedbackMessage.html.twig', array(
//                         'feedbackForm' => $form->createView()
//         ));
//     }
    
    public function sendAction(Request $request)
    {
        if('POST' != $request->getMethod()) {
            return new Response("Save requires POST method!", 405);
        }
        $feedbackMessage = new FeedbackMessage();
        $form = $this->createForm(New FeedbackMessageFormType(), $feedbackMessage);
        $form->bind($request);
        
        if ($form->isValid()) {
          
            $em = $this->getDoctrine()->getEntityManager();
            $feedbackMessage->setRemoteAddress($request->server->get('REMOTE_ADDR'));
            $feedbackMessage->setHttpUseAgent($request->server->get('HTTP_USER_AGENT'));
            
            $em->persist($feedbackMessage);
            $em->flush();
    
            // dispatch event
            $eventName = InstitutionBundleEvents::ON_ADD_FEEDBACK_MESSAGE;
            $this->get('event_dispatcher')->dispatch($eventName, $this->get('events.factory')->create($eventName, $feedbackMessage));
            
            return new Response(\json_encode(true),200, array('content-type' => 'application/json'));
            
        }else{
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
}