<?php

namespace HealthCareAbroad\FrontendBundle\Controller;

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

class DefaultController extends Controller
{
    private $resultsPerPage = 15;

    public function indexAction(Request $request)
    {
        return $this->render('FrontendBundle:Default:index.html.twig');
    }

    public function renderNewsletterFormAction()
    {
        $newsletterSubscriber = new NewsletterSubscriber();
        $form = $this->createForm(new NewsletterSubscriberFormType(), $newsletterSubscriber);
        
        return $this->render('FrontendBundle:Widgets:footer.subscribeNewsletter.html.twig', array(
                        'subscribeNewsletterForm' => $form->createView()
        ));
    }

    public function treatmentListAction()
    {
        $institutionSpecializationRepo = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionSpecialization');
        $params['specializations'] = $this->getDoctrine()->getRepository('TermBundle:SearchTerm')->findAllActiveTermsGroupedBySpecialization();
        //$params['specializations'] = $institutionSpecializationRepo->getAllActiveSpecializations();

        return $this->render('FrontendBundle:Default:listTreatments.html.twig', $params);
    }

    public function destinationListAction()
    {
        $params['countries'] = $this->get('services.location')->getActiveCountriesWithCities();

        return $this->render('FrontendBundle:Default:listDestinations.html.twig', $params);
    }

    /**
     * TODO - Improved Implementation!
     *
     * Generate Frontend Breadcrumbs based on route name
     *
     * @author Adelbert D. Silla
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function renderBreadcrumbAction()
    {
        $request = $this->getRequest();
        $route = $request->get('route');
        $routeParams = $request->get('routeParams');
        $templateParams = array();

        switch($route) {

            case 'frontend_single_center_institution_profile' :
            case 'frontend_multiple_center_institution_profile' :
                $institution = $request->get('institution');
                $country = $institution->getCountry();
                $templateParams['breadcrumbs'][] = array(
                    'url' => $this->generateUrl('frontend_search_results_countries', array('country' => $country->getSlug())),
                    'label' => $country->getName()
                );

                if($city = $institution->getCity()) {
                    $templateParams['breadcrumbs'][] = array(
                        'url' => $this->generateUrl('frontend_search_results_cities', array('country' => $country->getSlug(),'city' => $city->getSlug())),
                        'label' => $city->getName());
                }

                $templateParams['breadcrumbs'][] = array('label' => $institution->getName());

                break;

            case 'frontend_institutionMedicaCenter_profile' :
                $institutionMedicalCenter = $request->get('institutionMedicalCenter');
                $institution = $institutionMedicalCenter->getInstitution();
                $institutionRoute = $this->get('services.institution')->getInstitutionRouteName($institution);
                $country = $institution->getCountry();

                $templateParams['breadcrumbs'][] = array('url' => $this->generateUrl('frontend_search_results_countries', array('country' => $country->getSlug())), 'label' => $country->getName());

                if($city = $institution->getCity()) {
                    $templateParams['breadcrumbs'][] = array(
                        'url' => $this->generateUrl('frontend_search_results_cities', array('country' => $country->getSlug(),'city' => $city->getSlug())),
                        'label' => $city->getName());
                }

                $templateParams['breadcrumbs'][] = array('url' => $this->generateUrl($institutionRoute, array('institutionSlug' => $institution->getSlug())), 'label' => $institution->getName());
                $templateParams['breadcrumbs'][] = array('label' => $institutionMedicalCenter->getName());
                break;

            case 'frontend_search_results_countries' :
                $country = $request->get('country');
                $templateParams['breadcrumbs'] = array(
                    array('label' => $country->getName()),
                );
                break;

            case 'frontend_search_results_cities' :
                $country = $request->get('country');
                $city = $request->get('city');
                if(!$country) {
                    $country = $city->getCountry();
                }

                $templateParams['breadcrumbs'] = array(
                    array('url' => $this->generateUrl('frontend_search_results_countries', array('country' => $country->getSlug())), 'label' => $country->getName()),
                    array('label' => $city->getName()),
                );
                break;

            case 'frontend_search_results_specializations' :
                $specialization = $request->get('specialization');
                $templateParams['breadcrumbs'] = array(
                    array('label' => $specialization->getName())
                );

                break;

            case 'frontend_search_results_treatments' :
                $treatment = $request->get('treatment');
                $specialization = $treatment->getSpecialization();

                $templateParams['breadcrumbs'] = array(
                    array('url' => $this->generateUrl('frontend_search_results_specializations', array('specialization' => $specialization->getSlug())), 'label' => $specialization->getName()),
                    array('label' => $treatment->getName())
                );
                break;
                
            case 'frontend_search_combined' :
                $country = $request->get('country');
                if ($request->get('specialization')) {
                    $specialization = $request->get('specialization');
                }
                elseif ($subSpecialization = $request->get('subSpecialization')) {
                    $specialization = $subSpecialization->getSpecialization();
                }

                $city = $request->get('city');
                $treatment = $request->get('treatment');

                if($city && !$country) { $country = $city->getCountry(); }
                if($treatment && !isset($specialization)) { $specialization = $treatment->getSpecialization(); }

                $breadcrumbs[] = array('url' => $this->generateUrl('frontend_search_results_countries', array('country' => $country->getSlug())), 'label' => $country->getName());

                if($city) {
                    $breadcrumbs[] = array(
                        'url' => $this->generateUrl('frontend_search_results_cities',
                            array('country' => $country->getSlug(), 'city' => $city->getSlug())),
                        'label' => $city->getName()
                    );

                    $breadcrumbs[] = array(
                        'url' => $this->generateUrl('frontend_search_combined_countries_cities_specializations',
                                        array('country' => $country->getSlug(), 'city' => $city->getSlug(), 'specialization' => $specialization->getSlug())),
                        'label' => $specialization->getName(),
                    );

                } else {
                    $breadcrumbs[] = array(
                        'url' => $this->generateUrl('frontend_search_combined_countries_specializations',
                                        array('specialization' => $specialization->getSlug(), 'country' => $country->getSlug())),
                        'label' => $specialization->getName(),
                    );
                }

                if($treatment) {
                    $breadcrumbs[] = array('label' => $treatment->getName());
                }

                $templateParams['breadcrumbs'] = $breadcrumbs;
                break;

            default :

                if(isset($routeParams['breadcrumbLabel'])) {
                    $templateParams['breadcrumbs'] = array(array('label' => $routeParams['breadcrumbLabel']));
                }

                //$templateParams['breadcrumbs'] = array(array('label' => 'Test'));
                break;
        }

        //var_dump($route); var_dump($request->get('routeParams'));

        return $this->render('FrontendBundle:Widgets:breadcrumbs.html.twig', $templateParams);
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
        $response = $this->render('SearchBundle:Frontend:resultsCombination.html.twig', array(
                        'searchResults' => new Pager($pagerAdapter, array('page' => $request->get('page'), 'limit' => $this->resultsPerPage)),
                        'searchLabel' => $country->getName() . ' - ' . $specialization->getName(),
                        'country' => $country,
                        'specialization' => $specialization,
                        'includedNarrowSearchWidgets' => array('sub_specialization', 'treatment', 'city'),
                        'narrowSearchParameters' => array(SearchParameterBag::FILTER_COUNTRY => $country->getId(), SearchParameterBag::FILTER_SPECIALIZATION => $specialization->getId())
        ));

        $response->headers->setCookie($this->buildCookie(array(
                        'countryId' => $country->getId(),
                        'specializationId' => $specialization->getId()
        )));

        return $response;
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
        $response = $this->render('SearchBundle:Frontend:resultsCombination.html.twig', array(
                        'searchResults' => new Pager($pagerAdapter, array('page' => $request->get('page'), 'limit' => $this->resultsPerPage)),
                        'searchLabel' => $country->getName() . ' - ' . $subSpecialization->getName(),
                        'country' => $country,
                        'subSpecialization' => $subSpecialization,
                        'includedNarrowSearchWidgets' => array('treatment', 'city'),
                        'narrowSearchParameters' => array(SearchParameterBag::FILTER_COUNTRY => $country->getId(), SearchParameterBag::FILTER_SUBSPECIALIZATION => $subSpecialization->getId(), SearchParameterBag::FILTER_SPECIALIZATION => $subSpecialization->getSpecialization()->getId())
        ));

        $response->headers->setCookie($this->buildCookie(array(
                        'countryId' => $country->getId(),
                        'specializationId' => $subSpecialization->getSpecialization()->getId(),
                        'subSpecializationId' => $subSpecialization->getId()
        )));

        return $response;
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
        $response = $this->render('SearchBundle:Frontend:resultsCombination.html.twig', array(
                        'searchResults' => new Pager($pagerAdapter, array('page' => $request->get('page'), 'limit' => $this->resultsPerPage)),
                        'searchLabel' => $country->getName() . ' - ' . $treatment->getName(),
                        'country' => $country,
                        'specialization' => $treatment->getSpecialization(),
                        'treatment' => $treatment,
                        'includedNarrowSearchWidgets' => array('city'),
                        'narrowSearchParameters' => array(SearchParameterBag::FILTER_COUNTRY => $country->getId(), SearchParameterBag::FILTER_TREATMENT => $treatment->getId(), SearchParameterBag::FILTER_SPECIALIZATION => $treatment->getSpecialization()->getId())
        ));

        $response->headers->setCookie($this->buildCookie(array(
                        'countryId' => $country->getId(),
                        'specializationId' => $treatment->getSpecialization()->getId(),
                        'treatmentId' => $treatment->getId())));

        return $response;
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
        $response = $this->render('SearchBundle:Frontend:resultsCombination.html.twig', array(
                        'searchResults' => new Pager($pagerAdapter, array('page' => $request->get('page'), 'limit' => $this->resultsPerPage)),
                        'searchLabel' => $city->getName() . ', ' . $city->getCountry()->getName() . ' - ' . $specialization->getName(),
                        'specialization' => $specialization,
                        'city' => $city,
                        'includedNarrowSearchWidgets' => array('sub-specialization', 'treatment'),
                        'narrowSearchParameters' => array(SearchParameterBag::FILTER_COUNTRY => $city->getCountry()->getId(), SearchParameterBag::FILTER_CITY => $city->getId(), SearchParameterBag::FILTER_SPECIALIZATION => $specialization->getId())
        ));

        $response->headers->setCookie($this->buildCookie(array(
                        'countryId' => $city->getCountry()->getId(),
                        'cityId' => $city->getId(),
                        'specializationId' => $specialization->getId()
        )));

        return $response;
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
        $response = $this->render('SearchBundle:Frontend:resultsCombination.html.twig', array(
                        'searchResults' => new Pager($pagerAdapter, array('page' => $request->get('page'), 'limit' => $this->resultsPerPage)),
                        'searchLabel' => $city->getName() . ', ' . $city->getCountry()->getName() . ' - ' . $subSpecialization->getName(),
                        'subSpecialization' => $subSpecialization,
                        'city' => $city,
                        'includedNarrowSearchWidgets' => array('treatment'),
                        'narrowSearchParameters' => array(SearchParameterBag::FILTER_COUNTRY => $city->getCountry()->getId(), SearchParameterBag::FILTER_CITY => $city->getId(), SearchParameterBag::FILTER_SUBSPECIALIZATION => $subSpecialization->getId(), SearchParameterBag::FILTER_SPECIALIZATION => $specialization->getId())
        ));

        $response->headers->setCookie($this->buildCookie(array(
                        'countryId' => $city->getCountry()->getId(),
                        'cityId' => $city->getId(),
                        'specializationId' => $specialization->getId(),
                        'subSpecializationId' => $subSpecialization->getId()
        )));

        return $response;
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
        $response = $this->render('SearchBundle:Frontend:resultsCombination.html.twig', array(
                    'searchResults' => new Pager($pagerAdapter, array('page' => $request->get('page'), 'limit' => $this->resultsPerPage)),
                    'searchLabel' => $city->getName() . ', ' . $city->getCountry()->getName() . ' - ' . $treatment->getName(),
                    'treatment' => $treatment,
                    'city' => $city,
                    'includedNarrowSearchWidgets' => array(),
                        // broaden search only
        ));

        $response->headers->setCookie($this->buildCookie(array(
                        'countryId' => $city->getCountry()->getId(),
                        'cityId' => $city->getId(),
                        'specializationId' => $treatment->getSpecialization()->getId(),
                        'treatmentId' => $treatment->getId()
        )));

        return $response;
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

    public function ajaxSendErrorReportAction(){
        $output = array();
        $request = $this->getRequest();
        $em = $this->getDoctrine()->getEntityManager();

        $errorReport = new ErrorReport();
        $form = $this->createForm(new ErrorReportFormType(), $errorReport);

        if ($request->isMethod('POST')) {
             $form->bind($request);
             if ($form->isValid()) {
                try {
                    $errorReport->setLoggedUserId(0);
                    $errorReport->setStatus(ErrorReport::STATUS_ACTIVE);
                    $errorReport->setFlag(ErrorReport::FRONTEND_REPORT);
                    $em->persist($errorReport);
                    $em->flush();

                    //// create event on sendEmail and dispatch
                    $event = new CreateErrorReportEvent($errorReport);
                    $this->get('event_dispatcher')->dispatch(ErrorReportEvent::ON_CREATE_REPORT, $event);

                    $output = "Your report has been submitted. Thank you.";
                    $response = new Response(\json_encode($output), 200, array('content-type' => 'application/json'));

                }
                catch(\Exception $e) {
                    $response = new Response('Error: '.$e->getMessage(), 500);
                }
            }
            else {
                $response = new Response('Form error'.$e->getMessage(), 400);
            }
        }

        return $response;
    }
}
