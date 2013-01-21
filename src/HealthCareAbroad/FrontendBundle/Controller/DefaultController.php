<?php

namespace HealthCareAbroad\FrontendBundle\Controller;

use HealthCareAbroad\PagerBundle\Pager;

use HealthCareAbroad\PagerBundle\Adapter\ArrayAdapter;

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
    private $resultsPerPage = 15;

    public function indexAction(Request $request)
    {
        $advertisementRepo = $this->getDoctrine()->getRepository('AdvertisementBundle:AdvertisementDenormalizedProperty');

        $highlightAds = $advertisementRepo->getActiveHomepagePremier();
        $featuredClinicAds = $advertisementRepo->getActiveFeaturedClinic();
        $news = $advertisementRepo->getActiveNews();
        $commonTreatments = $advertisementRepo->getCommonTreatments();


        $params = array(
            'highlightAds' => $highlightAds,
            'highlight' => $highlightAds && count($highlightAds) ? $highlightAds[array_rand($highlightAds)] : null,
            'featuredClinicAds' => $featuredClinicAds,
            'commonTreatments' => $commonTreatments,
            'destinationAds' => array(),
            'news' => $news,
            'searchParams' => array()
        );
        //var_dump($params['highlight']->getInstitution()->getLogo()); exit;
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
     all entries that have that specific controller. Or you can delete the entry
     on the table. Deletion is safe, the only drawback is when that route is
     accessed it will have to be stored again.
     **************************************************************************/
    public function commonLandingAction()
    {
        //throw $this->createNotFoundException('An error occurred while processing your request.');

        echo __METHOD__ . '<br/>';
        echo 'Route parameters:<br/>';
        var_dump($this->getRequest()->attributes->get('_route_params'));
        exit;
    }

    public function listCountrySpecializationAction(Request $request)
    {
        $parameters = $request->attributes->get('_route_params');

        $em = $this->getDoctrine()->getManager();

        $country = $em->getRepository('HelperBundle:Country')->find($parameters['countryId']);
        $specialization = $em->getRepository('TreatmentBundle:Specialization')->find($parameters['specializationId']);

        $searchResults = $em->getRepository('InstitutionBundle:InstitutionMedicalCenter')->getMedicalCentersBySpecializationAndCountry($specialization, $country);

        //TODO: This is temporary; use OrmAdapter
        $adapter = new ArrayAdapter($searchResults);
        $searchResults = new Pager($adapter, array('page' => $request->get('page'), 'limit' => 1));

        $searchLabel = $country->getName() . ' - ' . $specialization->getName();

        $response = $this->render('SearchBundle:Frontend:resultsCombination.html.twig', array(
                        'searchResults' => $searchResults,
                        'searchLabel' => $searchLabel
        ));

        $response->headers->setCookie($this->buildCookie(array('countryId' => $country->getId(), 'specializationId' => $specialization->getId())));

        return $response;
    }

    public function listCountrySubSpecializationAction(Request $request)
    {
        $parameters = $request->attributes->get('_route_params');

        $em = $this->getDoctrine()->getManager();

        $country = $em->getRepository('HelperBundle:Country')->find($parameters['countryId']);
        $subSpecialization = $em->getRepository('TreatmentBundle:SubSpecialization')->find($parameters['subSpecializationId']);

        //TODO: pager
        $searchResults = $em->getRepository('InstitutionBundle:InstitutionMedicalCenter')->getMedicalCentersBySubSpecializationAndCountry($subSpecialization, $country);
        $searchLabel = $country->getName() . ' - ' . $subSpecialization->getName();

        $response = $this->render('SearchBundle:Frontend:resultsCombination.html.twig', array(
                        'searchResults' => $searchResults,
                        'searchLabel' => $searchLabel
        ));

        $response->headers->setCookie($this->buildCookie(array('countryId' => $country->getId(), 'subSpecializationId' => $subSpecialization->getId())));

        return $response;
    }

    public function listCountryTreatmentAction(Request $request)
    {
        $parameters = $request->attributes->get('_route_params');

        $em = $this->getDoctrine()->getManager();

        $country = $em->getRepository('HelperBundle:Country')->find($parameters['countryId']);
        $treatment = $em->getRepository('TreatmentBundle:Treatment')->find($parameters['treatmentId']);

        //TODO: pager
        $searchResults = $em->getRepository('InstitutionBundle:InstitutionMedicalCenter')->getMedicalCentersByTreatmentAndCountry($treatment, $country);
        $searchLabel = $country->getName() . ' - ' . $treatment->getName();

        $response = $this->render('SearchBundle:Frontend:resultsCombination.html.twig', array(
                        'searchResults' => $searchResults,
                        'searchLabel' => $searchLabel
        ));

        $response->headers->setCookie($this->buildCookie(array('countryId' => $country->getId(), 'treatmentId' => $treatment->getId())));

        return $response;
    }

    public function listCitySpecializationAction(Request $request)
    {
        $parameters = $request->attributes->get('_route_params');

        $em = $this->getDoctrine()->getManager();

        $city = $em->getRepository('HelperBundle:City')->find($parameters['cityId']);
        $specialization = $em->getRepository('TreatmentBundle:Specialization')->find($parameters['specializationId']);

        //TODO: pager
        $searchResults = $em->getRepository('InstitutionBundle:InstitutionMedicalCenter')->getMedicalCentersBySpecializationAndCity($specialization, $city);
        $searchLabel = $city->getName() . ', ' . $city->getCountry()->getName() . ' - ' . $specialization->getName();

        $response = $this->render('SearchBundle:Frontend:resultsCombination.html.twig', array(
                        'searchResults' => $searchResults,
                        'searchLabel' => $searchLabel
        ));

        $response->headers->setCookie($this->buildCookie(array('cityId' => $city->getId(), 'specializationId' => $specialization->getId())));

        return $response;
    }

    public function listCitySubSpecializationAction(Request $request)
    {
        $parameters = $request->attributes->get('_route_params');

        $em = $this->getDoctrine()->getManager();

        $city = $em->getRepository('HelperBundle:City')->find($parameters['cityId']);
        $subSpecialization = $em->getRepository('TreatmentBundle:SubSpecialization')->find($parameters['subSpecializationId']);

        //TODO: pager
        $searchResults = $em->getRepository('InstitutionBundle:InstitutionMedicalCenter')->getMedicalCentersBySubSpecializationAndCity($subSpecialization, $city);
        $searchLabel = $city->getName() . ', ' . $city->getCountry()->getName() . ' - ' . $subSpecialization->getName();

        $response = $this->render('SearchBundle:Frontend:resultsCombination.html.twig', array(
                        'searchResults' => $searchResults,
                        'searchLabel' => $searchLabel
        ));

        $response->headers->setCookie($this->buildCookie(array('cityId' => $city->getId(), 'subSpecializationId' => $subSpecialization->getId())));

        return $response;
    }

    public function listCityTreatmentAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $parameters = $request->attributes->get('_route_params');

        $city = $em->getRepository('HelperBundle:City')->find($parameters['cityId']);
        $treatment = $em->getRepository('TreatmentBundle:Treatment')->find($parameters['treatmentId']);

        $response = $this->render('SearchBundle:Frontend:resultsCombination.html.twig', array(
            'searchResults' => $em->getRepository('InstitutionBundle:InstitutionMedicalCenter')->getMedicalCentersByTreatmentAndCity($treatment, $city),
            'searchLabel' => $city->getName() . ', ' . $city->getCountry()->getName() . ' - ' . $treatment->getName()
        ));

        $response->headers->setCookie($this->buildCookie(array('cityId' => $city->getId(), 'treatmentId' => $treatment->getId())));

        return $response;
    }

    private function buildCookie(array $values)
    {
        //TODO: ttl
        return new Cookie(md5($this->getRequest()->getPathInfo()), json_encode($values));

        // maybe we should update the cookie from time to time to reset ttl?
        //if (!$this->getRequest()->cookies->has($cookieName)) {
        //    return new Cookie($cookieName, json_encode($values));
        //}
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
