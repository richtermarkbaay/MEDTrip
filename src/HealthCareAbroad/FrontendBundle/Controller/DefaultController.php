<?php

namespace HealthCareAbroad\FrontendBundle\Controller;

use Symfony\Component\HttpFoundation\Cookie;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use HealthCareAbroad\AdminBundle\Entity\ErrorReport;

use HealthCareAbroad\FrontendBundle\Entity\NewsletterSubscriber;

use HealthCareAbroad\HelperBundle\Form\ErrorReportFormType;

use HealthCareAbroad\FrontendBundle\Form\NewsletterSubscriberFormType;

use HealthCareAbroad\HelperBundle\Event\CreateErrorReportEvent;

use HealthCareAbroad\HelperBundle\Event\ErrorReportEvent;

use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    public function indexAction(Request $request)
    {
        $advertisementRepo = $this->getDoctrine()->getRepository('AdvertisementBundle:AdvertisementDenormalizedProperty');

        $highlightAds = $advertisementRepo->getActiveHomepagePremier();
        $featuredClinicAds = $advertisementRepo->getActiveFeaturedClinic();
        $news = $advertisementRepo->getActiveNews();
        $commonTreatments = $advertisementRepo->getCommonTreatments();

        
        $params = array(
            'highlightAds' => $highlightAds,
            'highlight' => $highlightAds[array_rand($highlightAds)],
            'featuredClinicAds' => $featuredClinicAds,
            'commonTreatments' => $commonTreatments,
            'destinationAds' => array(),
            'news' => $news,
            'searchParams' => array()
        );

        return $this->render('FrontendBundle:Default:index.html.twig', $params);
    }

    /*
     * Newsletter subscribe
     * @author Chaztine Blance
     */
    public function newAction()
    {
        if($this->getRequest()->attributes->get('_route_params')){

            return $this->redirect($this->generateUrl('main_homepage_index_html'));
        }

        //get IP Address
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

                        $this->get('session')->setFlash('success', "Thank you for signing up!");
                }
                catch (\Exception $e) {

                    $request->getSession()->setFlash("error", "Failed. Please try again.");
                    $redirectUrl = $this->generateUrl("main_homepage_index_html");
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
     Take note, however, that if this is in production you will have to manually
     change the controller field in the frontend_routes table in the database for
     all entries that have that specific controller.
     **************************************************************************/
    public function commonLandingAction()
    {
        echo 'Route parameters:<br/>';
        var_dump($this->getRequest()->attributes->get('_route_params'));
        exit;
    }

    public function listCentersForCountryAction(Request $request)
    {
        $parameters = $request->attributes->get('_route_params');

        $em = $this->getDoctrine()->getManager();

        $country = $em->getRepository('Helper:Country')->find($parameters['countryId']);
        $medicalCenter = $em->getRepository('MedicalProcedure:MedicalCenter')->find($parameters['centerId']);

        $response = new Response();
        $response->headers->setCookie(
            new Cookie('/'.'', $user, 0, '/', null, false, false)
        );

        $institutionMedicalCenters = $em->getRepository('Institution:InstitutionMedicalCenter')
            ->getCentersWithLocation($medicalCenter, $country);
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

    public function viewTreatmentAction()
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
