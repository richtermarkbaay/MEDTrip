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
        $featuredDestinations = $advertisementRepo->getFeaturedDestinations(); 
        
        $params = array(
            'highlightAds' => $highlightAds,
            'highlight' => $highlightAds && count($highlightAds) ? $highlightAds[array_rand($highlightAds)] : null,
            //'highlight' => $this->getDoctrine()->getRepository('AdvertisementBundle:AdvertisementDenormalizedProperty')->find(52),
            'featuredClinicAds' => $featuredClinicAds,
            'commonTreatments' => $commonTreatments,
            'destinationAds' => $featuredDestinations,
            'news' => $news,
            'searchParams' => array()
        );
        //var_dump($params['highlight']->getInstitution()->getLogo()); exit;
        return $this->render('FrontendBundle:Default:index.html.twig', $params);
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
                $city = $institution->getCity();
                $templateParams['breadcrumbs'] = array(
                    array('url' => $this->generateUrl('frontend_search_results_countries', array('country' => $country->getSlug())), 'label' => $country->getName()),
                    array('url' => $this->generateUrl('frontend_search_results_cities', array('country' => $country->getSlug(),'city' => $city->getSlug())), 'label' => $city->getName()),
                    array('label' => $institution->getName())
                );
                break;

            case 'frontend_institutionMedicaCenter_profile' :
                $institutionMedicalCenter = $request->get('institutionMedicalCenter');
                $institution = $institutionMedicalCenter->getInstitution();
                $institutionRoute = $this->get('services.institution')->getInstitutionRouteName($institution);
                $country = $institution->getCountry();
                $city = $institution->getCity();

                $templateParams['breadcrumbs'] = array(
                    array('url' => $this->generateUrl('frontend_search_results_countries', array('country' => $country->getSlug())), 'label' => $country->getName()),
                    array('url' => $this->generateUrl('frontend_search_results_cities', array('country' => $country->getSlug(), 'city' => $city->getSlug())), 'label' => $city->getName()),
                    array('url' => $this->generateUrl($institutionRoute, array('institutionSlug' => $institution->getSlug())), 'label' => $institution->getName()),
                    array('label' => $institutionMedicalCenter->getName())
                );

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
                $specialization = $request->get('specialization');
                $city = $request->get('city');
                $treatment = $request->get('treatment');

                if($city && !$country) { $country = $city->getCountry(); }
                if($treatment && !$specialization) { $specialization = $treatment->getSpecialization(); }

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
        $em = $this->getDoctrine()->getManager();

        $parameters = $request->attributes->get('_route_params');
        $country = $em->getRepository('HelperBundle:Country')->find($parameters['countryId']);
        $specialization = $em->getRepository('TreatmentBundle:Specialization')->find($parameters['specializationId']);

        //TODO: This is temporary; use OrmAdapter
        $pagerAdapter = new ArrayAdapter($em->getRepository('InstitutionBundle:InstitutionMedicalCenter')->getMedicalCentersBySpecializationAndCountry($specialization, $country));
        $response = $this->render('SearchBundle:Frontend:resultsCombination.html.twig', array(
                        'searchResults' => new Pager($pagerAdapter, array('page' => $request->get('page'), 'limit' => $this->resultsPerPage)),
                        'searchLabel' => $country->getName() . ' - ' . $specialization->getName(),
                        'country' => $country,
                        'specialization' => $specialization
        ));

        $response->headers->setCookie($this->buildCookie(array(
                        'countryId' => $country->getId(),
                        'specializationId' => $specialization->getId()
        )));

        return $response;
    }

    public function listCountrySubSpecializationAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $parameters = $request->attributes->get('_route_params');
        $country = $em->getRepository('HelperBundle:Country')->find($parameters['countryId']);
        $subSpecialization = $em->getRepository('TreatmentBundle:SubSpecialization')->find($parameters['subSpecializationId']);

        $pagerAdapter = new ArrayAdapter($em->getRepository('InstitutionBundle:InstitutionMedicalCenter')->getMedicalCentersBySubSpecializationAndCountry($subSpecialization, $country));
        $response = $this->render('SearchBundle:Frontend:resultsCombination.html.twig', array(
                        'searchResults' => new Pager($pagerAdapter, array('page' => $request->get('page'), 'limit' => $this->resultsPerPage)),
                        'searchLabel' => $country->getName() . ' - ' . $subSpecialization->getName()

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
        $em = $this->getDoctrine()->getManager();

        $parameters = $request->attributes->get('_route_params');
        $country = $em->getRepository('HelperBundle:Country')->find($parameters['countryId']);
        $treatment = $em->getRepository('TreatmentBundle:Treatment')->find($parameters['treatmentId']);

        $pagerAdapter = new ArrayAdapter($em->getRepository('InstitutionBundle:InstitutionMedicalCenter')->getMedicalCentersByTreatmentAndCountry($treatment, $country));
        $response = $this->render('SearchBundle:Frontend:resultsCombination.html.twig', array(
                        'searchResults' => new Pager($pagerAdapter, array('page' => $request->get('page'), 'limit' => $this->resultsPerPage)),
                        'searchLabel' => $country->getName() . ' - ' . $treatment->getName(),
                        'country' => $country,
                        'treatment' => $treatment
        ));

        $response->headers->setCookie($this->buildCookie(array(
                        'countryId' => $country->getId(),
                        'specializationId' => $treatment->getSpecialization()->getId(),
                        'treatmentId' => $treatment->getId())));

        return $response;
    }

    public function listCitySpecializationAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $parameters = $request->attributes->get('_route_params');
        $city = $em->getRepository('HelperBundle:City')->find($parameters['cityId']);
        $specialization = $em->getRepository('TreatmentBundle:Specialization')->find($parameters['specializationId']);

        $pagerAdapter = new ArrayAdapter($em->getRepository('InstitutionBundle:InstitutionMedicalCenter')->getMedicalCentersBySpecializationAndCity($specialization, $city));
        $response = $this->render('SearchBundle:Frontend:resultsCombination.html.twig', array(
                        'searchResults' => new Pager($pagerAdapter, array('page' => $request->get('page'), 'limit' => $this->resultsPerPage)),
                        'searchLabel' => $city->getName() . ', ' . $city->getCountry()->getName() . ' - ' . $specialization->getName(),
                        'specialization' => $specialization,
                        'city' => $city
        ));

        $response->headers->setCookie($this->buildCookie(array(
                        'countryId' => $city->getCountry()->getId(),
                        'cityId' => $city->getId(),
                        'specializationId' => $specialization->getId()
        )));

        return $response;
    }

    // TODO - Not working Yet!
    public function listCitySubSpecializationAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $parameters = $request->attributes->get('_route_params');
        $city = $em->getRepository('HelperBundle:City')->find($parameters['cityId']);
        $subSpecialization = $em->getRepository('TreatmentBundle:SubSpecialization')->find($parameters['subSpecializationId']);

        $pagerAdapter = new ArrayAdapter($em->getRepository('InstitutionBundle:InstitutionMedicalCenter')->getMedicalCentersBySubSpecializationAndCity($subSpecialization, $city));
        $response = $this->render('SearchBundle:Frontend:resultsCombination.html.twig', array(
                        'searchResults' => new Pager($pagerAdapter, array('page' => $request->get('page'), 'limit' => $this->resultsPerPage)),
                        'searchLabel' => $city->getName() . ', ' . $city->getCountry()->getName() . ' - ' . $subSpecialization->getName(),
                        'subSpecialization' => $subSpecialization,
                        'treatment' => $treatment,
                        'city' => $city
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
        $em = $this->getDoctrine()->getManager();
        $parameters = $request->attributes->get('_route_params');

        $city = $em->getRepository('HelperBundle:City')->find($parameters['cityId']);
        $treatment = $em->getRepository('TreatmentBundle:Treatment')->find($parameters['treatmentId']);

        $pagerAdapter = new ArrayAdapter($em->getRepository('InstitutionBundle:InstitutionMedicalCenter')->getMedicalCentersByTreatmentAndCity($treatment, $city));
        $response = $this->render('SearchBundle:Frontend:resultsCombination.html.twig', array(
                    'searchResults' => new Pager($pagerAdapter, array('page' => $request->get('page'), 'limit' => $this->resultsPerPage)),
                    'searchLabel' => $city->getName() . ', ' . $city->getCountry()->getName() . ' - ' . $treatment->getName(),
                    'treatment' => $treatment,
                    'city' => $city
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
