<?php

namespace HealthCareAbroad\InstitutionBundle\Controller;

use HealthCareAbroad\InstitutionBundle\Event\InstitutionBundleEvents;

use HealthCareAbroad\InstitutionBundle\Event\InstitutionEvent;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use HealthCareAbroad\HelperBundle\Form\FeedbackFormType;
use HealthCareAbroad\HelperBundle\Entity\Feedback;

class FeedbackController extends InstitutionAwareController
{
    public function addAction()
    {
        $form = $this->createForm(New FeedbackFormType(), new Feedback());
    
        return $this->render('InstitutionBundle:Feedback:form.html.twig', array(
            'id' => null,
            'form' => $form->createView(),
            'formAction' => $this->generateUrl('institution_feedback_create'),
            'institution' => $this->institution,
        ));
    }

    public function saveAction()
    {
  
        $request = $this->getRequest();
        $session = $this->getRequest()->getSession();
        if('POST' != $request->getMethod()) {
            return new Response("Save requires POST method!", 405);
        }

        $em = $this->getDoctrine()->getEntityManager();

        $feedback = new Feedback();

        $form = $this->createForm(New FeedbackFormType(), $feedback);
           $form->bind($request);

           if ($form->isValid()) {
               $feedback->setAccountId($session->get('accountId'));
               $feedback->setInstitution($this->institution);
               $em->persist($feedback);
               $em->flush($feedback);

               // dispatch event
               $eventName = InstitutionBundleEvents::ON_ADD_INSTITUTION_FEEDBACK ;
               $this->get('event_dispatcher')->dispatch($eventName, $this->get('events.factory')->create($eventName, $feedback));
               
               $request->getSession()->setFlash('success', 'Your form was successfully submitted. Thank you.');

               return $this->redirect($this->generateUrl('institution_feedback_add'));
        }

        $formAction =  $this->generateUrl('institution_feedback_create');

        return $this->render('InstitutionBundle:Feedback:form.html.twig', array(
                'id' => $id,
                'form' => $form->createView(),
                'formAction' => $formAction
        ));
    }
}