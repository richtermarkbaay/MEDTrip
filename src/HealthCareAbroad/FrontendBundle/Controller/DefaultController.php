<?php

namespace HealthCareAbroad\FrontendBundle\Controller;

use HealthCareAbroad\FrontendBundle\Services\FrontendMemcacheKeys;

use HealthCareAbroad\HelperBundle\Services\PageMetaConfigurationService;

use HealthCareAbroad\SearchBundle\Services\SearchUrlGenerator;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use HealthCareAbroad\SearchBundle\Services\SearchParameterBag;

use HealthCareAbroad\InstitutionBundle\Services\InstitutionMedicalCenterService;

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

class DefaultController extends ResponseHeadersController
{
    private $resultsPerPage = 20;

    public function indexAction(Request $request)
    {
        $start = \microtime(true);
        $memcacheService = $this->get('services.memcache');

        $homepagePremierAdsCacheKey = FrontendMemcacheKeys::HOMEPAGE_PREMIER_ADS_KEY;
        $featuredClinicsAdsCacheKey = FrontendMemcacheKeys::HOMEPAGE_FEATURED_CLINICS_ADS_KEY;
        $featuredDestinationsAdsCacheKey = FrontendMemcacheKeys::HOMEPAGE_FEATURED_DESTINATIONS_ADS_KEY;
        $featuredPostsCacheKey = FrontendMemcacheKeys::HOMEPAGE_FEATURED_POSTS_ADS_KEY;
        $commonTreatmentsCacheKey = FrontendMemcacheKeys::HOMEPAGE_COMMON_TREATMENTS_ADS_KEY;
        $featuredVideoCacheKey = FrontendMemcacheKeys::HOMEPAGE_FEATURED_VIDEO_ADS_KEY;

        if(!($ads['homepagePremier'] = $memcacheService->get($homepagePremierAdsCacheKey))) {
            $ads['homepagePremier'] = $this->get('twig.advertisement_widgets')->renderHomepagePremierAds();
            $memcacheService->set($homepagePremierAdsCacheKey, $ads['homepagePremier']);
        }

        if(!($ads['featuredClinics'] = $memcacheService->get($featuredClinicsAdsCacheKey))) {
            $ads['featuredClinics'] = $this->get('twig.advertisement_widgets')->renderHomepageFeaturedClinicsAds();
            $memcacheService->set($featuredClinicsAdsCacheKey, $ads['featuredClinics']);
        }

        if(!($ads['featuredDestinations'] = $memcacheService->get($featuredDestinationsAdsCacheKey))) {
            $ads['featuredDestinations'] = $this->get('twig.advertisement_widgets')->renderHomepageFeaturedDestinationsAds();
            $memcacheService->set($featuredDestinationsAdsCacheKey, $ads['featuredDestinations']);
        }
        
        if(!($ads['featuredPosts'] = $memcacheService->get($featuredPostsCacheKey))) {
            $ads['featuredPosts'] = $this->get('twig.advertisement_widgets')->renderHomepageFeaturedPostsAds();
            $memcacheService->set($featuredPostsCacheKey, $ads['featuredPosts']);
        }

        if(!($ads['commonTreatments'] = $memcacheService->get($commonTreatmentsCacheKey))) {
            $ads['commonTreatments'] = $this->get('twig.advertisement_widgets')->renderHomepageCommonTreatmentsAds();
            $memcacheService->set($commonTreatmentsCacheKey, $ads['commonTreatments']);
        }

        if(!($ads['featuredVideo'] = $memcacheService->get($featuredVideoCacheKey))) {
            $ads['featuredVideo'] = $this->get('twig.advertisement_widgets')->renderHomepageFeaturedVideoAd();
            $memcacheService->set($featuredVideoCacheKey, $ads['featuredVideo']);
        }        

        $response = $this->setResponseHeaders($this->render('FrontendBundle:Default:index.optimized.html.twig', array('ads' => $ads)));
        $end = \microtime(true);$diff = $end-$start;
        //echo "{$diff}s"; exit;

        return $response;
    }

    public function indexBakAction(Request $request)
    {
        $start = \microtime(true);
        $response = $this->setResponseHeaders($this->render('FrontendBundle:Default:index.html.twig'));
        $end = \microtime(true);$diff = $end-$start;
        echo "{$diff}s"; exit;
        return $response;
    }

//     public function renderNewsletterFormAction()
//     {
//         $newsletterSubscriber = new NewsletterSubscriber();
//         $form = $this->createForm(new NewsletterSubscriberFormType(), $newsletterSubscriber);

//         return $this->render('FrontendBundle:Widgets:footer.subscribeNewsletter.html.twig', array(
//                         'subscribeNewsletterForm' => $form->createView()
//         ));
//     }

    public function treatmentListAction()
    {
        $institutionSpecializationRepo = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionSpecialization');
        $params['specializations'] = $this->getDoctrine()->getRepository('TermBundle:SearchTerm')->findAllActiveTermsGroupedBySpecialization();
        //$params['specializations'] = $institutionSpecializationRepo->getAllActiveSpecializations();

        return $this->setResponseHeaders($this->render('FrontendBundle:Default:listTreatments.html.twig', $params));
    }

    public function destinationListAction()
    {
        $params['countries'] = $this->get('services.terms')->getActiveCountriesWithCities();

        return $this->setResponseHeaders($this->render('FrontendBundle:Default:listDestinations.html.twig', $params));
    }

    /**
     * TODO: validate email
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function subscribeNewsletterAction(Request $request)
    {
        $subscriber = $request->get('newsletter_subscriber');

        $response = array();
        if ($this->get('services.mailchimp')->listSubscribe($subscriber['email'])) {
            $response['success'] = 1;
            $response['message'] = 'Thank you! Please check your email to confirm your subscription.';
        } else {
            $response['success'] = 0;
            $response['message'] = 'An error occurred while processing your request. Please try again.';
        }

        return new Response(json_encode($response), 200, array('Content-Type'=>'application/json'));
    }


    /*
     * Newsletter subscribe
     * @author Chaztine Blance
     */
    public function newAction()
    {
        if($this->getRequest()->attributes->get('_route_params')){

            return $this->redirect($this->generateUrl('frontend_main_homepage'));
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
                        $referer = $request->server->has('HTTP_REFERER') ? $request->server->get('HTTP_REFERER') : '';
//                         $splashPageUrl = $this->generateUrl('splash_page', array(), true);
//                         $redirectUrl =  $splashPageUrl == $referer || $referer == ''
//                             ?  $splashPageUrl
//                             : $referer;
                        $redirectUrl = $referer != '' ? $referer : $this->generateUrl('frontend_main_homepage');
                        return $this->redirect($redirectUrl);
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

    /**
     * TODO: Should this be on the search bundle?
     *
     * @param Request $request
     */
    public function listCountrySpecializationAction(Request $request)
    {
        $parameters = $request->attributes->get('_route_params');

        $em = $this->getDoctrine()->getManager();
        if (!$country = $em->getRepository('HelperBundle:Country')->find(isset($parameters['countryId']) ? $parameters['countryId'] : $parameters['country'])) {
            throw new NotFoundHttpException();
        }
        if (!$specialization = $em->getRepository('TreatmentBundle:Specialization')->find(isset($parameters['specializationId']) ? $parameters['specializationId'] : $parameters['specialization'])) {
            throw new NotFoundHttpException();
        }

        //TODO: This is temporary; use OrmAdapter
        //$pagerAdapter = new ArrayAdapter($em->getRepository('InstitutionBundle:InstitutionMedicalCenter')->getMedicalCentersBySpecializationAndCountry($specialization, $country));
        $pagerAdapter = new ArrayAdapter($em->getRepository('TermBundle:SearchTerm')->findByFilters(array($specialization, $country)));
        $pager = new Pager($pagerAdapter, array('page' => $request->get('page'), 'limit' => $this->resultsPerPage));

        // set total results for page metas
        $request->attributes->set('pageMetaVariables', array(PageMetaConfigurationService::CLINIC_RESULTS_COUNT_VARIABLE => $pager->getTotalResults()));
        $request->attributes->set('searchObjects', array(
            SearchUrlGenerator::SEARCH_URL_PARAMETER_SPECIALIZATION => $specialization,
            SearchUrlGenerator::SEARCH_URL_PARAMETER_COUNTRY => $country,
        ));
        $this->setBreadcrumbRequestAttributes($request, array('country' => $country, 'specialization' => $specialization));

        $response = $this->render('SearchBundle:Frontend:resultsCombination.html.twig', array(
            'searchResults' => $pager,
            'searchLabel' => array('treatment' => $specialization->getName(), 'destination' => $country->getName()),
            'country' => $country,
            'specialization' => $specialization,
            'includedNarrowSearchWidgets' => array('sub_specialization', 'destinations'),
            'narrowSearchParameters' => array(SearchParameterBag::FILTER_COUNTRY => $country->getId(), SearchParameterBag::FILTER_SPECIALIZATION => $specialization->getId()),
            'featuredClinicParams' => array('countryId' => $country->getId(),'specializationId' => $specialization->getId())
        ));

        $response->headers->setCookie($this->buildCookie(array('countryId' => $country->getId(), 'specializationId' => $specialization->getId())));

        return $this->setResponseHeaders($response);
    }

    public function listCountrySubSpecializationAction(Request $request)
    {
        $parameters = $request->attributes->get('_route_params');

        $em = $this->getDoctrine()->getManager();
        if (!$country = $em->getRepository('HelperBundle:Country')->find(isset($parameters['countryId']) ? $parameters['countryId'] : $parameters['country'])) {
            throw new NotFoundHttpException();
        }
        if (!$subSpecialization = $em->getRepository('TreatmentBundle:SubSpecialization')->find(isset($parameters['subSpecializationId']) ? $parameters['subSpecializationId'] : $parameters['subSpecialization'])) {
            throw new NotFoundHttpException();
        }

        //$pagerAdapter = new ArrayAdapter($em->getRepository('InstitutionBundle:InstitutionMedicalCenter')->getMedicalCentersBySubSpecializationAndCountry($subSpecialization, $country));
        $pagerAdapter = new ArrayAdapter($em->getRepository('TermBundle:SearchTerm')->findByFilters(array($subSpecialization, $country)));
        $pager = new Pager($pagerAdapter, array('page' => $request->get('page'), 'limit' => $this->resultsPerPage));

        // set total results for page metas
        $request->attributes->set('pageMetaVariables', array(PageMetaConfigurationService::CLINIC_RESULTS_COUNT_VARIABLE => $pager->getTotalResults()));
        $request->attributes->set('searchObjects', array(
            SearchUrlGenerator::SEARCH_URL_PARAMETER_SPECIALIZATION => $subSpecialization->getSpecialization(),
            SearchUrlGenerator::SEARCH_URL_PARAMETER_SUB_SPECIALIZATION => $subSpecialization,
            SearchUrlGenerator::SEARCH_URL_PARAMETER_COUNTRY => $country,
        ));
        $this->setBreadcrumbRequestAttributes($request, array('country' => $country, 'specialization' => $subSpecialization->getSpecialization(), 'subSpecialization' => $subSpecialization));

        $response = $this->render('SearchBundle:Frontend:resultsCombination.html.twig', array(
            'searchResults' => $pager,
            'searchLabel' => array('treatment' => $subSpecialization->getName(), 'destination' => $country->getName()),
            'country' => $country,
            'subSpecialization' => $subSpecialization,
            'includedNarrowSearchWidgets' => array('treatment', 'destinations'),
            'narrowSearchParameters' => array(SearchParameterBag::FILTER_COUNTRY => $country->getId(), SearchParameterBag::FILTER_SUBSPECIALIZATION => $subSpecialization->getId(), SearchParameterBag::FILTER_SPECIALIZATION => $subSpecialization->getSpecialization()->getId()),
            'featuredClinicParams' => array('countryId' => $country->getId(),'subSpecializationId' => $subSpecialization->getId())
        ));

        $response->headers->setCookie($this->buildCookie(array(
                        'countryId' => $country->getId(),
                        'specializationId' => $subSpecialization->getSpecialization()->getId(),
                        'subSpecializationId' => $subSpecialization->getId()
        )));

        return $this->setResponseHeaders($response);
    }

    public function listCountryTreatmentAction(Request $request)
    {
        $parameters = $request->attributes->get('_route_params');

        $em = $this->getDoctrine()->getManager();
        if (!$country = $em->getRepository('HelperBundle:Country')->find(isset($parameters['countryId']) ? $parameters['countryId'] : $parameters['country'])) {
            throw new NotFoundHttpException();
        }

        if (!$treatment = $em->getRepository('TreatmentBundle:Treatment')->find(isset($parameters['treatmentId']) ? $parameters['treatmentId'] : $parameters['treatment'])) {
            throw new NotFoundHttpException();
        }

        //$pagerAdapter = new ArrayAdapter($em->getRepository('InstitutionBundle:InstitutionMedicalCenter')->getMedicalCentersByTreatmentAndCountry($treatment, $country));
        $pagerAdapter = new ArrayAdapter($em->getRepository('TermBundle:SearchTerm')->findByFilters(array($treatment, $country)));
        $pager = new Pager($pagerAdapter, array('page' => $request->get('page'), 'limit' => $this->resultsPerPage));

        // set total results for page metas
        $request->attributes->set('pageMetaVariables', array(PageMetaConfigurationService::CLINIC_RESULTS_COUNT_VARIABLE => $pager->getTotalResults()));
        $request->attributes->set('searchObjects', array(
            SearchUrlGenerator::SEARCH_URL_PARAMETER_SPECIALIZATION => $treatment->getSpecialization(),
            SearchUrlGenerator::SEARCH_URL_PARAMETER_TREATMENT => $treatment,
            SearchUrlGenerator::SEARCH_URL_PARAMETER_COUNTRY => $country,
        ));
        $this->setBreadcrumbRequestAttributes($request, array('country' => $country, 'specialization' => $treatment->getSpecialization(), 'treatment' => $treatment));

        $response = $this->render('SearchBundle:Frontend:resultsCombination.html.twig', array(
            'searchResults' => $pager,
            'searchLabel' => array('treatment' => $treatment->getName(), 'destination' => $country->getName()),
            'country' => $country,
            'specialization' => $treatment->getSpecialization(),
            'treatment' => $treatment,
            'includedNarrowSearchWidgets' => array('destinations'),
            'narrowSearchParameters' => array(SearchParameterBag::FILTER_COUNTRY => $country->getId(), SearchParameterBag::FILTER_TREATMENT => $treatment->getId(), SearchParameterBag::FILTER_SPECIALIZATION => $treatment->getSpecialization()->getId()),
            'featuredClinicParams' => array('countryId' => $country->getId(), 'treatmentId' => $treatment->getId())
        ));

        $response->headers->setCookie($this->buildCookie(array(
                        'countryId' => $country->getId(),
                        'specializationId' => $treatment->getSpecialization()->getId(),
                        'treatmentId' => $treatment->getId())));

        return $this->setResponseHeaders($response);
    }

    public function listCitySpecializationAction(Request $request)
    {
        $parameters = $request->attributes->get('_route_params');

        $em = $this->getDoctrine()->getManager();
        if (!$city = $em->getRepository('HelperBundle:City')->find(isset($parameters['cityId']) ? $parameters['cityId'] : $parameters['city'])) {
            throw new NotFoundHttpException();
        }
        if (!$specialization = $em->getRepository('TreatmentBundle:Specialization')->find(isset($parameters['specializationId']) ? $parameters['specializationId'] : $parameters['specialization'])) {
            throw new NotFoundHttpException();
        }

        //$pagerAdapter = new ArrayAdapter($em->getRepository('InstitutionBundle:InstitutionMedicalCenter')->getMedicalCentersBySpecializationAndCity($specialization, $city));
        $pagerAdapter = new ArrayAdapter($em->getRepository('TermBundle:SearchTerm')->findByFilters(array($specialization, $city)));
        $pager = new Pager($pagerAdapter, array('page' => $request->get('page'), 'limit' => $this->resultsPerPage));
        // set total results for page metas
        $request->attributes->set('pageMetaVariables', array(PageMetaConfigurationService::CLINIC_RESULTS_COUNT_VARIABLE => $pager->getTotalResults()));
        $request->attributes->set('searchObjects', array(
            SearchUrlGenerator::SEARCH_URL_PARAMETER_SPECIALIZATION => $specialization,
            SearchUrlGenerator::SEARCH_URL_PARAMETER_COUNTRY => $city->getCountry(),
            SearchUrlGenerator::SEARCH_URL_PARAMETER_CITY => $city,
        ));
        $this->setBreadcrumbRequestAttributes($request, array('country' => $city->getCountry(), 'city' => $city, 'specialization' => $specialization));

        $response = $this->render('SearchBundle:Frontend:resultsCombination.html.twig', array(
                        'searchResults' => $pager,
                        'searchLabel' => array('treatment' => $specialization->getName(), 'destination' => $city->getName() . ', ' . $city->getCountry()->getName()),
                        'specialization' => $specialization,
                        'city' => $city,
                        'includedNarrowSearchWidgets' => array('sub-specialization'),
                        'narrowSearchParameters' => array(SearchParameterBag::FILTER_COUNTRY => $city->getCountry()->getId(), SearchParameterBag::FILTER_CITY => $city->getId(), SearchParameterBag::FILTER_SPECIALIZATION => $specialization->getId()),
                        'featuredClinicParams' => array('cityId' => $city->getId(),'specializationId' => $specialization->getId())
        ));

        $response->headers->setCookie($this->buildCookie(array(
                        'countryId' => $city->getCountry()->getId(),
                        'cityId' => $city->getId(),
                        'specializationId' => $specialization->getId()
        )));

        return $this->setResponseHeaders($response);
    }

    public function listCitySubSpecializationAction(Request $request)
    {
        $parameters = $request->attributes->get('_route_params');

        $em = $this->getDoctrine()->getManager();
        if (!$city = $em->getRepository('HelperBundle:City')->find(isset($parameters['cityId']) ? $parameters['cityId'] : $parameters['city'])) {
            throw new NotFoundHttpException();
        }
        if (!$specialization = $em->getRepository('TreatmentBundle:Specialization')->find(isset($parameters['specializationId']) ? $parameters['specializationId'] : $parameters['specialization'])) {
            throw new NotFoundHttpException();
        }
        if (!$subSpecialization = $em->getRepository('TreatmentBundle:SubSpecialization')->find(isset($parameters['subSpecializationId']) ? $parameters['subSpecializationId'] : $parameters['subSpecialization'])) {
            throw new NotFoundHttpException();
        }

        //$pagerAdapter = new ArrayAdapter($em->getRepository('InstitutionBundle:InstitutionMedicalCenter')->getMedicalCentersBySubSpecializationAndCity($subSpecialization, $city));
        $pagerAdapter = new ArrayAdapter($em->getRepository('TermBundle:SearchTerm')->findByFilters(array($subSpecialization, $city)));
        $pager = new Pager($pagerAdapter, array('page' => $request->get('page'), 'limit' => $this->resultsPerPage));

        // set total results for page metas
        $request->attributes->set('pageMetaVariables', array(PageMetaConfigurationService::CLINIC_RESULTS_COUNT_VARIABLE => $pager->getTotalResults()));
        $request->attributes->set('searchObjects', array(
            SearchUrlGenerator::SEARCH_URL_PARAMETER_SPECIALIZATION => $specialization,
            SearchUrlGenerator::SEARCH_URL_PARAMETER_SUB_SPECIALIZATION => $subSpecialization,
            SearchUrlGenerator::SEARCH_URL_PARAMETER_COUNTRY => $city->getCountry(),
            SearchUrlGenerator::SEARCH_URL_PARAMETER_CITY => $city
        ));
        $this->setBreadcrumbRequestAttributes($request, array('country' => $city->getCountry(), 'city' => $city, 'specialization' => $specialization, 'subSpecialization' => $subSpecialization));

        $response = $this->render('SearchBundle:Frontend:resultsCombination.html.twig', array(
            'searchResults' => $pager,
            'searchLabel' => array('treatment' => $subSpecialization->getName(), 'destination' => $city->getName() . ', ' . $city->getCountry()->getName()),
            'subSpecialization' => $subSpecialization,
            'city' => $city,
            'includedNarrowSearchWidgets' => array('treatment'),
            'narrowSearchParameters' => array(SearchParameterBag::FILTER_COUNTRY => $city->getCountry()->getId(), SearchParameterBag::FILTER_CITY => $city->getId(), SearchParameterBag::FILTER_SUBSPECIALIZATION => $subSpecialization->getId(), SearchParameterBag::FILTER_SPECIALIZATION => $specialization->getId()),
            'featuredClinicParams' => array('cityId' => $city->getId(),'subSpecializationId' => $subSpecialization->getId())
        ));

        $response->headers->setCookie($this->buildCookie(array(
            'countryId' => $city->getCountry()->getId(),
            'cityId' => $city->getId(),
            'specializationId' => $specialization->getId(),
            'subSpecializationId' => $subSpecialization->getId()
        )));

        return $this->setResponseHeaders($response);
    }

    public function listCityTreatmentAction(Request $request)
    {
        $parameters = $request->attributes->get('_route_params');

        $em = $this->getDoctrine()->getManager();
        if (!$city = $em->getRepository('HelperBundle:City')->find(isset($parameters['cityId']) ? $parameters['cityId'] : $parameters['city'])) {
            throw new NotFoundHttpException();
        }
        if (!$treatment = $em->getRepository('TreatmentBundle:Treatment')->find(isset($parameters['treatmentId']) ? $parameters['treatmentId'] : $parameters['treatment'])) {
            throw new NotFoundHttpException();
        }

        //$pagerAdapter = new ArrayAdapter($em->getRepository('InstitutionBundle:InstitutionMedicalCenter')->getMedicalCentersByTreatmentAndCity($treatment, $city));
        $pagerAdapter = new ArrayAdapter($em->getRepository('TermBundle:SearchTerm')->findByFilters(array($treatment, $city)));
        $pager = new Pager($pagerAdapter, array('page' => $request->get('page'), 'limit' => $this->resultsPerPage));

        // set total results for page metas
        $request->attributes->set('pageMetaVariables', array(PageMetaConfigurationService::CLINIC_RESULTS_COUNT_VARIABLE => $pager->getTotalResults()));
        $request->attributes->set('searchObjects', array(
            SearchUrlGenerator::SEARCH_URL_PARAMETER_SPECIALIZATION => $treatment->getSpecialization(),
            SearchUrlGenerator::SEARCH_URL_PARAMETER_TREATMENT => $treatment,
            SearchUrlGenerator::SEARCH_URL_PARAMETER_COUNTRY => $city->getCountry(),
            SearchUrlGenerator::SEARCH_URL_PARAMETER_CITY => $city
        ));
        $this->setBreadcrumbRequestAttributes($request, array('country' => $city->getCountry(), 'city' => $city, 'specialization' => $treatment->getSpecialization(), 'treatment' => $treatment));

        $response = $this->render('SearchBundle:Frontend:resultsCombination.html.twig', array(
            'searchResults' => $pager,
            'searchLabel' => array('treatment' => $treatment->getName(), 'destination' => $city->getName() . ', ' . $city->getCountry()->getName()),
            'treatment' => $treatment,
            'city' => $city,
            'includedNarrowSearchWidgets' => array(),
            'featuredClinicParams' => array('cityId' => $city->getId(),'treatmentId' => $treatment->getId())
        ));

        $response->headers->setCookie($this->buildCookie(array(
                        'countryId' => $city->getCountry()->getId(),
                        'cityId' => $city->getId(),
                        'specializationId' => $treatment->getSpecialization()->getId(),
                        'treatmentId' => $treatment->getId()
        )));

        return $this->setResponseHeaders($response);
    }

    private function buildCookie(array $values)
    {
        // ttl = 3 months
        return new Cookie(md5($this->getRequest()->getPathInfo()), json_encode($values), time() + 7776000);
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
    /**
     * Call a 404 page
     */
    public function call404ExcemptionAction(){
        throw $this->createNotFoundException("Only supports AJAX request");
    }

    /**
     *
     * @throws \Exception 500
     */
    public function call500ExcemptionAction(){

        throw new \Exception('Something went wrong!');
    }

    /**
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
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
                $errorReport->setFlag(ErrorReport::FRONTEND_REPORT);
                $em->persist($errorReport);
                $em->flush();

                return new Response(\json_encode(true),200, array('content-type' => 'application/json'));
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

    //TODO: move to service layer
    private function setBreadcrumbRequestAttributes(Request $request, array $attributes)
    {
        foreach ($attributes as $key => $value) {
            $request->attributes->set($key, array('name' => $value->getName(), 'slug' => $value->getSlug()));
        }
    }
}
