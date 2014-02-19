<?php

namespace HealthCareAbroad\FrontendBundle\Controller;

use Symfony\Component\EventDispatcher\GenericEvent;

use HealthCareAbroad\MailerBundle\Event\MailerBundleEvents;

use HealthCareAbroad\FrontendBundle\FrontendBundleEvents;

use HealthCareAbroad\FrontendBundle\Form\InstitutionInquiryFormType;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionInquiry;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use HealthCareAbroad\FrontendBundle\Form\InquiryType;
use HealthCareAbroad\AdminBundle\Entity\Inquiry;
use HealthCareAbroad\UserBundle\Entity\SiteUser;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use HealthCareAbroad\InstitutionBundle\Services\InstitutionInquiryService;
class InquiryController extends Controller
{
    /**
     * TODO: is this used or is this deprecated already?
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $form = $this->createForm(new InquiryType());

        if ($this->getRequest()->isMethod('POST')) {

            $form->bindRequest($this->getRequest());

            if ($form->isValid()) {

                //create inquire
                $inquire = new Inquiry();
                $inquire->setFirstName($form->get('firstName')->getData());
                $inquire->setLastName($form->get('lastName')->getData());
                $inquire->setEmail($form->get('email')->getData());
                $inquire->setInquirySubject($form->get('inquiry_subject')->getData());
                $inquire->setMessage($form->get('message')->getData());
                $inquire->setStatus(SiteUser::STATUS_ACTIVE);
                $inquire = $this->get('services.inquire')->createInquiry($inquire);

                if ( count($inquire) > 0 ) {
                    $this->get('session')->setFlash('notice', "Successfully submitted.");
                }
                else
                {
                    $this->get('session')->setFlash('notice', "Unable to send inqueries!");
                }
            } else {

            }
        }

        return $this->render('FrontendBundle:Inquiry:index.html.twig', array(
                'form' => $form->createView(),
        ));
    }

    /**
     * TODO: rename function since this is misleading
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function ajaxSaveInquiryAction(Request $request)
    {
        $institutionInquiry = new InstitutionInquiry();
        $form = $this->createForm(new InstitutionInquiryFormType(), $institutionInquiry);

        $form->bindRequest($request);

        if ($form->isValid()) {
            if($request->get('imcId')) {
                $imc = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionMedicalCenter')->find($request->get('imcId'));
                $institution = $imc->getInstitution();
                $institutionInquiry->setInstitutionMedicalCenter($imc);
            }
            else {
                $institution = $this->getDoctrine()->getRepository('InstitutionBundle:Institution')->find($request->get('institutionId'));
            }
            $institutionInquiry->setInstitution($institution);
            $institutionInquiry->setRemoteAddress($request->server->get('REMOTE_ADDR'));
            $institutionInquiry->setHttpUseAgent($request->server->get('HTTP_USER_AGENT'));
            $institutionInquiry->setStatus(InstitutionInquiry::STATUS_UNAPPROVED);
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($institutionInquiry);
            $em->flush();

            $subscribed = false;
            if ($form->get('newsletterSubscription')->getData()) {
                $subscribed = $this->get('services.mailchimp')->listSubscribe($institutionInquiry->getInquirerEmail());
            }
            $responseData = array(
                'institutionInquiry' => InstitutionInquiryService::toArray($institutionInquiry),
                'subscribed' => !($subscribed === false)
            );
            $response = new Response(\json_encode($responseData), 200, array('content-type' => 'application/json'));
        }
        else {

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
