<?php

namespace HealthCareAbroad\FrontendBundle\Controller;

use HealthCareAbroad\FrontendBundle\FrontendBundleEvents;

use HealthCareAbroad\AdminBundle\Entity\Inquiry;

use HealthCareAbroad\FrontendBundle\Form\InquiryType;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class CommonPageController extends ResponseHeadersController
{
    public function viewPrivacyPolicyAction(){

        return $this->setStaticPageResponseHeaders($this->render('FrontendBundle:Static:privacyPolicy.html.twig'));
    }

    public function viewTermsOfUseAction(){

        return $this->setStaticPageResponseHeaders($this->render('FrontendBundle:Static:termsOfUse.html.twig'));
    }

    public function viewAboutUsAction(){

        return $this->setStaticPageResponseHeaders($this->render('FrontendBundle:Static:aboutUs.html.twig'));
    }

    public function saveInquiryAction(Request $request)
    {
        $inquiry = new Inquiry();
        $form = $this->createForm(new InquiryType(), $inquiry);
        $inquirySubjects = $this->getDoctrine()->getRepository('AdminBundle:InquirySubject')->findAll();

        if($request->isMethod('POST')) {
            $form->bind($request);
            if($form->isValid()) {

                $inquiry->setRemoteAddress($request->server->get('REMOTE_ADDR'));
                $inquiry->setHttpUseAgent($request->server->get('HTTP_USER_AGENT'));
                $inquiry->setStatus(Inquiry::STATUS_ACTIVE);

                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($inquiry);
                $em->flush();
                $request->getSession()->setFlash('success', 'Your message has been sent! Thank you.');
                
                // dispatch event
                $event = $this->get('events.factory')->create(FrontendBundleEvents::ADD_INQUIRY, $inquiry);
                $this->get('event_dispatcher')->dispatch(FrontendBundleEvents::ADD_INQUIRY, $event);
                
                return $this->redirect($this->generateUrl('frontend_page_inquiry'));
            } else {
                $params['hasErrors'] = true;
            }
        }

        $params['form'] = $form->createView();
        $params['inquirySubjects'] = $inquirySubjects;

        return $this->render('FrontendBundle:Static:inquiry.html.twig', $params);
    }
}