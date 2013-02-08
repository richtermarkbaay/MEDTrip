<?php
namespace HealthCareAbroad\SearchBundle\Controller;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use HealthCareAbroad\HelperBundle\Entity\City;
use HealthCareAbroad\HelperBundle\Entity\Country;
use HealthCareAbroad\HelperBundle\Repository\CountryRepository;
use HealthCareAbroad\HelperBundle\Repository\CityRepository;
use HealthCareAbroad\PagerBundle\Pager;
use HealthCareAbroad\PagerBundle\Adapter\ArrayAdapter;
use HealthCareAbroad\SearchBundle\Services\SearchParameterBag;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * TODO: Refactor whole class
 *
 */
class FrontendController extends Controller
{
    private $resultsPerPage = 15;

    public function showWidgetAction(Request $request)
    {
        $options['context'] = $request->get('context');
        if ($request->get('subContext', '')) {
            $options['subContext'] = $request->get('subContext');
        }

        $options['destinationId'] = '0-0';
        $options['destinationLabel'] = '';
        $options['treatmentId'] = '0';
        $options['treatmentLabel'] = '';

        switch ($options['context']) {
            case 'homepage':
                $template = 'SearchBundle:Frontend/Widgets:mainSearchWidget.html.twig';

                break;

            case 'destinations':
                $options['destinationId'] = $request->get('destinationId');
                $template = 'SearchBundle:Frontend/Widgets:resultsPageSearchWidget.html.twig';

                if ($request->get('subContext', '') == 'specialization') {
                    $options['autocompleteRoute'] = 'frontend_search_ajaxLoadSpecializations';
                } elseif ($request->get('subContext') == 'subSpecialization') {
                    $options['autocompleteRoute'] = 'frontend_search_ajaxLoadSubSpecializations';
                } elseif ($request->get('subContext') == 'treatment') {
                    $options['autocompleteRoute'] = 'frontend_search_ajaxLoadTreatments';
                }

                break;

            case 'treatments':
                $options['treatmentId'] = $request->get('treatmentId');
                $template = 'SearchBundle:Frontend/Widgets:resultsPageSearchWidget.html.twig';

                if ($request->get('subContext', '') == 'country') {
                    $options['autocompleteRoute'] = 'frontend_search_ajaxLoadCountries';
                } elseif ($request->get('subContext') == 'city') {
                    $options['autocompleteRoute'] = 'frontend_search_ajaxLoadCities';
                }

                break;

            case 'sidebar':
                $template = 'SearchBundle:Frontend/Widgets:sidebarSearchWidget.html.twig';

                break;

            default:
                throw new \Exception('Undefined context');
        }

        return $this->render($template, array('options' => $options));
    }

    /**
     * TODO: refactor
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function searchAction(Request $request)
    {
        $searchParams = $this->getSearchParams($request, true);

        $sessionVariables = array();

        switch ($searchParams->get('context')) {
            case SearchParameterBag::SEARCH_TYPE_DESTINATIONS:
                $routeParameters['country'] = $this->getDoctrine()->getEntityManager()->getRepository('HelperBundle:Country')->find($searchParams->get('countryId'))->getSlug();
                $route = 'frontend_search_results_countries';
                $sessionVariables['countryId'] = $searchParams->get('countryId');

                if ($searchParams->get('cityId')) {
                    $routeParameters['city'] = $this->getDoctrine()->getEntityManager()->getRepository('HelperBundle:City')->find($searchParams->get('cityId'))->getSlug();
                    $route = 'frontend_search_results_cities';
                    $sessionVariables['cityId'] = $searchParams->get('cityId');
                }

                break;

            case SearchParameterBag::SEARCH_TYPE_TREATMENTS:
                $termDocuments = $this->get('services.search')->getTermDocuments($searchParams);

                if (count($termDocuments) == 1) {
                    $termDocument = $termDocuments[0];
                    $routeParameters['specialization'] = $this->getDoctrine()->getEntityManager()->getRepository('TreatmentBundle:Specialization')->find($termDocument['specialization_id'])->getSlug();
                    $route = 'frontend_search_results_specializations';
                    $sessionVariables = array('specializationId' => $termDocument['specialization_id'], 'termId' => $termDocument['term_id']);

                    if ($termDocument['treatment_id']) {
                        $routeParameters['treatment'] = $this->getDoctrine()->getEntityManager()->getRepository('TreatmentBundle:Treatment')->find($termDocument['treatment_id'])->getSlug();
                        $route = 'frontend_search_results_treatments';
                        $sessionVariables['treatmentId'] = $termDocument['treatment_id'];
                    } elseif ($termDocument['sub_specialization_id']) {
                        $routeParameters['subSpecialization'] = $this->getDoctrine()->getEntityManager()->getRepository('TreatmentBundle:SubSpecialization')->find($termDocument['sub_specialization_id'])->getSlug();
                        $route = 'frontend_search_results_subSpecializations';
                        $sessionVariables['subSpecializationId'] = $termDocument['sub_specialization_id'];
                    }
                } elseif ($termDocuments) {
                    $term = $this->get('services.search')->getTerm($searchParams->get('treatmentId'));

                    $routeParameters = array('tag' => $term['slug']);
                    $route = 'frontend_search_results_related';
                    $sessionVariables = array('termId' => $term['id']);

                } else {
                    throw new NotFoundHttpException();
                }

                break;

            case SearchParameterBag::SEARCH_TYPE_COMBINATION:

                //TODO: underlying query still needs finetuning
                $termDocuments = $this->get('services.search')->getTermDocuments($searchParams);

                if (count($termDocuments) == 1) {
                    $termDocument = $termDocuments[0];

                    //we don't have configured routes for this type of search so we need to generate the url manually
                    $url = $this->buildUrl($searchParams, $termDocument);

                    //This code is needed when working in dev environment
                    if (get_class($this->container) === 'appDevDebugProjectContainer') {
                        $url = '/app_dev.php'.$url;
                    }
                    //will be used by our router listener
                    $request->getSession()->set(md5($url), json_encode($this->transformParams($termDocument)));

                } elseif ($termDocuments) {
                    $term = $this->get('services.search')->getTerm($searchParams->get('treatmentId'));

                    $routeParameters = array('tag' => $term['slug']);
                    $route = 'frontend_search_results_related';
                    $sessionVariables = array('termId' => $term['id']);

                } else {
                    throw new NotFoundHttpException();
                }

                return $this->redirect($url);

            default:

                throw new NotFoundHttpException();
                //return new RedirectResponse($request->headers->get('referer'));
        }

        // this is used to avoid using slugs after redirection
        $request->getSession()->set('search_terms', json_encode($sessionVariables));

        return $this->redirect($this->generateUrl($route, $routeParameters));
    }

    public function searchResultsCountriesAction(Request $request)
    {
        $searchTerms = json_decode($request->getSession()->remove('search_terms'), true);

        $country = $this->getDoctrine()->getRepository('HelperBundle:Country')->getCountry(isset($searchTerms['countryId']) ? $searchTerms['countryId'] : $request->get('country'));

        //TODO: This is temporary; use OrmAdapter
        $adapter = new ArrayAdapter($this->get('services.search')->searchByCountry($country));
        $parameters = array(
            'searchResults' => new Pager($adapter, array('page' => $request->get('page'), 'limit' => $this->resultsPerPage)),
            'searchLabel' => isset($searchTerms['destinationLabel']) ? $searchTerms['destinationLabel'] : $country->getName(),
            'routeName' => 'frontend_search_results_countries',
            'paginationParameters' => array('country' => $country->getSlug()),
            'destinationId' => $country->getId() . '-0',
            'country' => $country
        );

        $prefix = $this->getPrefix();

        list($parameters['topSpecializations'], $parameters['topTreatments']) = array_map(
            function($treatments) use ($country, $prefix) {
                return FrontendController::appendTreatmentUrls($treatments, array('country' => $country), $prefix);
             }, $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionSpecialization')->getCountryTopTreatments($country)
        );

        return  $this->render('SearchBundle:Frontend:resultsDestinations.html.twig', $parameters);
    }

    public function searchResultsCitiesAction(Request $request)
    {
        $searchTerms = json_decode($request->getSession()->remove('search_terms'), true);

        $city = $this->getDoctrine()->getRepository('HelperBundle:City')->getCity(isset($searchTerms['cityId']) ? $searchTerms['cityId'] : $request->get('city'));

        //TODO: This is temporary; use OrmAdapter
        $adapter = new ArrayAdapter($this->get('services.search')->searchByCity($city));
        $parameters = array(
            'searchResults' => new Pager($adapter, array('page' => $request->get('page'), 'limit' => $this->resultsPerPage)),
            'searchLabel' => isset($searchTerms['destinationLabel']) ? $searchTerms['destinationLabel'] : $city->getName().', '.$city->getCountry()->getName(),
            'routeName' => 'frontend_search_results_cities',
            'paginationParameters' => array('city' => $city->getSlug(), 'country' => $city->getCountry()->getSlug()),
            'destinationId' => $city->getCountry()->getId() . '-' . $city->getId(),
            'city' => $city,
            'country' => $city->getCountry()
        );

        $prefix = $this->getPrefix();

        list($parameters['topSpecializations'], $parameters['topTreatments']) = array_map(
            function($treatments) use ($city, $prefix) {
                return FrontendController::appendTreatmentUrls($treatments, array('city' => $city, 'country' => $city->getCountry()), $prefix);
             }, $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionSpecialization')->getCityTopTreatments($city)
        );

        return $this->render('SearchBundle:Frontend:resultsDestinations.html.twig', $parameters);
    }

    public function searchResultsSpecializationsAction(Request $request)
    {
        $searchTerms = json_decode($request->getSession()->remove('search_terms'), true);
        $specialization = $this->getDoctrine()->getRepository('TreatmentBundle:Specialization')->getSpecialization(isset($searchTerms['specializationId']) ? $searchTerms['specializationId'] : $request->get('specialization'));

        if (isset($searchTerms['termId'])) {
            $termId = $searchTerms['termId'];
        } else {
            $term = $this->get('services.search')->getTerm($request->get('specialization'), array('column' => 'slug'));
            $termId = $term['id'];
        }

        //TODO: This is temporary; use OrmAdapter
        $adapter = new ArrayAdapter($this->get('services.search')->searchBySpecialization($specialization));
        $parameters = array(
            'searchResults' => new Pager($adapter, array('page' => $request->get('page'), 'limit' => $this->resultsPerPage)),
            'searchLabel' => isset($searchTerms['treatmentLabel']) ? $searchTerms['treatmentLabel'] : $specialization->getName(),
            'routeName' => 'frontend_search_results_specializations',
            'paginationParameters' => array('specialization' => $specialization->getSlug()),
            'treatmentId' => $termId,
            'specialization' => $specialization
        );

        $prefix = $this->getPrefix();

        list($parameters['topCountries'], $parameters['topCities']) = array_map(
            function($destinations) use ($specialization, $prefix) {
                return FrontendController::appendDestinationUrls($destinations, array('specialization' => $specialization), $prefix);
             }, $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionSpecialization')->getSpecializationTopDestinations($specialization)
         );

        return $this->render('SearchBundle:Frontend:resultsTreatments.html.twig', $parameters);
    }

    private function getPrefix()
    {
        $prefix = '';
        if (get_class($this->container) === 'appDevDebugProjectContainer') {
            $prefix = '/app_dev.php';
        }
        return $prefix;
    }

    public function searchResultsSubSpecializationsAction(Request $request)
    {
        $searchTerms = json_decode($request->getSession()->remove('search_terms'), true);

        $specialization = $this->getDoctrine()->getRepository('TreatmentBundle:Specialization')->getSpecialization(isset($searchTerms['specializationId']) ? $searchTerms['specializationId'] : $request->get('specialization'));
        $subSpecialization = $this->getDoctrine()->getRepository('TreatmentBundle:SubSpecialization')->getSubSpecialization(isset($searchTerms['subSpecializationId']) ? $searchTerms['subSpecializationId'] : $request->get('subSpecialization'));

        if (isset($searchTerms['termId'])) {
            $termId = $searchTerms['termId'];
        } else {
            $term = $this->get('services.search')->getTerm($request->get('subSpecialization'), array('column' => 'slug'));
            $termId = $term['id'];
        }

        //TODO: This is temporary; use OrmAdapter
        $adapter = new ArrayAdapter($this->get('services.search')->searchBySubSpecialization($searchTerms['subSpecializationId']));
        $parameters = array(
            'searchResults' => new Pager($adapter, array('page' => $request->get('page'), 'limit' => $this->resultsPerPage)),
            'searchLabel' => isset($searchTerms['treatmentLabel']) ? $searchTerms['treatmentLabel'] : $specialization->getName() . ' - ' . $subSpecialization->getName(),
            'routeName' => 'frontend_search_results_subSpecializations',
            'paginationParameters' => array('specialization' => $specialization->getSlug(), 'subSpecialization' => $subSpecialization->getSlug()),
            'treatmentId' => $termId,
            'specialization' => $specialization,
            'subSpecialization' => $subSpecialization
        );

        $prefix = $this->getPrefix();

        list($parameters['topCountries'], $parameters['topCities']) = array_map(
            function($destinations) use ($specialization, $subSpecialization, $prefix) {
                return FrontendController::appendDestinationUrls($destinations, array('specialization' => $specialization, 'subSpecialization' => $subSpecialization), $prefix);
             }, $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionSpecialization')->getSubSpecializationTopDestinations($subSpecialization)
        );

        return $this->render('SearchBundle:Frontend:resultsTreatments.html.twig', $parameters);
    }

    public function searchResultsTreatmentsAction(Request $request)
    {
        $searchTerms = json_decode($request->getSession()->remove('search_terms'), true);

        $specialization = $this->getDoctrine()->getRepository('TreatmentBundle:Specialization')->getSpecialization(isset($searchTerms['specializationId']) ? $searchTerms['specializationId'] : $request->get('specialization'));
        $treatment = $this->getDoctrine()->getRepository('TreatmentBundle:Treatment')->getTreatment(isset($searchTerms['treatmentId']) ? $searchTerms['treatmentId'] : $request->get('treatment'));

        if (isset($searchTerms['termId'])) {
            $termId = $searchTerms['termId'];
        } else {
            $term = $this->get('services.search')->getTerm($request->get('treatment'), array('column' => 'slug'));
            $termId = $term['id'];
        }

        //TODO: This is temporary; use OrmAdapter
        $adapter = new ArrayAdapter($this->get('services.search')->searchByTreatment($treatment));
        $parameters = array(
            'searchResults' => new Pager($adapter, array('page' => $request->get('page'), 'limit' => $this->resultsPerPage)),
            'searchLabel' => isset($searchTerms['treatmentLabel']) ? $searchTerms['treatmentLabel'] : $specialization->getName() . ' - ' . $treatment->getName(),
            'routeName' => 'frontend_search_results_treatments',
            'paginationParameters' => array('specialization' => $specialization->getSlug(), 'treatment' => $treatment->getSlug()),
            'treatmentId' => $termId,
            'treatment' => $treatment
        );

        $prefix = $this->getPrefix();

        list($parameters['topCountries'], $parameters['topCities']) = array_map(
            function($destinations) use ($specialization, $treatment, $prefix) {
                return FrontendController::appendDestinationUrls($destinations, array('specialization' => $specialization, 'treatment' => $treatment), $prefix);
            }, $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionSpecialization')->getTreatmentTopDestinations($treatment)
        );

        return $this->render('SearchBundle:Frontend:resultsTreatments.html.twig', $parameters);
    }

    public function searchResultsRelatedAction(Request $request)
    {
        $searchTerms = json_decode($request->getSession()->remove('search_terms'), true);

        if (isset($searchTerms['termId'])) {
            $term = $this->get('services.search')->getTerm($searchTerms['termId']);
        } else {
            $term = $this->get('services.search')->getTerm($searchTerms['termId'], array('column' => $request->get('tag')));
        }

        if (empty($term)) {
            throw new NotFoundHttpException();
        }

        //TODO: This is temporary; use OrmAdapter
        $adapter = new ArrayAdapter($this->get('services.search')->searchByTag($term['id']));

        return $this->render('SearchBundle:Frontend:resultsSectioned.html.twig', array(
                        'searchResults' => new Pager($adapter, array('page' => $request->get('page'), 'limit' => 1)),
                        'searchLabel' => $request->get('tag', ''),
                        'routeName' => 'frontend_search_results_related',
                        'paginationParameters' => array('tag' => $request->get('tag', '')),
                        'relatedTreatments' => $this->get('services.search')->getRelatedTreatments($term['id'])
        ));
    }

    public function ajaxLoadTreatmentsAction(Request $request)
    {
        $results = $this->get('services.search')->getTreatments($this->getSearchParams($request, true));

        return new Response(json_encode($results), 200, array('Content-Type'=>'application/json'));
    }

    public function ajaxLoadDestinationsAction(Request $request)
    {
        $results = $this->get('services.search')->getDestinations($this->getSearchParams($request, true));

        return new Response(json_encode($results), 200, array('Content-Type'=>'application/json'));
    }

    //TODO: create a dedicated class for this
    private function buildUrl($searchParams, $termDocument)
    {
        $combinationPrefix = 'country';
        $country = $this->getDoctrine()->getRepository('HelperBundle:Country')->find($searchParams->get('countryId'));

        if ($searchParams->get('cityId')) {
            $combinationPrefix = 'city';
            $city = $this->getDoctrine()->getRepository('HelperBundle:City')->find($searchParams->get('cityId'));
        }

        $combinationSuffix = '_specialization';
        $specialization = $this->getDoctrine()->getRepository('TreatmentBundle:Specialization')->find($termDocument['specialization_id']);

        if ($termDocument['treatment_id']) {
            $combinationSuffix = '_treatment';
        } elseif ($termDocument['sub_specialization_id']) {
            $combinationSuffix = '_subSpecialization';
        }

        switch ($combinationPrefix . $combinationSuffix) {
            case 'country_specialization':
                $url = '/'.$country->getSlug().
                '/'.$specialization->getSlug();
                break;

            case 'country_subSpecialization':
                $url = '/'.$country->getSlug().
                '/'.$specialization->getSlug().
                '/'.$this->getDoctrine()->getRepository('TreatmentBundle:SubSpecialization')->find($termDocument['sub_specialization_id'])->getSlug();
                break;

            case 'country_treatment':
                $url = '/'.$country->getSlug().
                '/'.$specialization->getSlug().
                '/'.$this->getDoctrine()->getRepository('TreatmentBundle:Treatment')->find($termDocument['treatment_id'])->getSlug().
                '/treatment';
                break;

            case 'city_specialization':
                $url = '/'.$country->getSlug().
                '/'.$city->getSlug().
                '/'.$specialization->getSlug();
                break;

            case 'city_subSpecialization':
                $url = '/'.$country->getSlug().
                '/'.$city->getSlug().
                '/'.$specialization->getSlug().
                '/'.$this->getDoctrine()->getRepository('TreatmentBundle:SubSpecialization')->find($termDocument['sub_specialization_id'])->getSlug();
                break;

            case 'city_treatment':
                $url = '/'.$country->getSlug().
                '/'.$city->getSlug().
                '/'.$specialization->getSlug().
                '/'.$this->getDoctrine()->getRepository('TreatmentBundle:Treatment')->find($termDocument['treatment_id'])->getSlug().
                '/treatment';
                break;
        }

        return $url;
    }

    private function transformParams($params) {
        $routeParams = array();

        if (!empty($params['specialization_id'])) {
            $routeParams['specializationId'] = $params['specialization_id'];
        }
        if (!empty($params['sub_specialization_id'])) {
            $routeParams['subSpecializationId'] = $params['sub_specialization_id'];
        }
        if (!empty($params['treatment_id'])) {
            $routeParams['treatmentId'] = $params['treatment_id'];
        }
        if (!empty($params['country_id'])) {
            $routeParams['countryId'] = $params['country_id'];
        }
        if (!empty($params['city_id'])) {
            $routeParams['cityId'] = $params['city_id'];
        }

        return $routeParams;
    }

    public static function appendDestinationUrls($locations, $treatment, $prefix = '')
    {
        $modifiedLocations = array();

        $treatmentUrlSegment = '/'.$treatment['specialization']->getSlug();

        if (isset($treatment['treatment'])) {
            $treatmentUrlSegment .= '/'.$treatment['treatment']->getSlug().'/treatment';
        } elseif (isset($treatment['subSpecialization'])) {
            $treatmentUrlSegment .= '/'.$treatment['subSpecialization']->getSlug();
        }

        foreach ($locations as $location) {
            $modifiedLocation = $location;

            if (isset($modifiedLocation['city_slug'])) {
                $modifiedLocation['url'] = $prefix.'/'.$modifiedLocation['country_slug'].'/'.$modifiedLocation['city_slug'].$treatmentUrlSegment;
            } else {
                $modifiedLocation['url'] = $prefix.'/'.$modifiedLocation['country_slug'].$treatmentUrlSegment;
            }

            $modifiedLocations[] = $modifiedLocation;
        }

        return $modifiedLocations;
    }

    public static function appendTreatmentUrls($treatments, $destination, $prefix = '')
    {
        $modifiedTreatments = array();

        $urlSegment = '/'.$destination['country']->getSlug();
        if (isset($destination['city'])) {
            $urlSegment .= '/'.$destination['city']->getSlug();
        }

        foreach ($treatments as $treatment) {
            $modifiedTreatment = $treatment;

            $url = $prefix.$urlSegment.'/'.$modifiedTreatment['specialization_slug'];
            if (isset($modifiedTreatment['treatment_slug'])) {
                $url .= '/'.$modifiedTreatment['treatment_slug'].'/treatment';
            } elseif (isset($modifiedTreatment['sub_specialization_slug'])) {
                $url .= '/'.$modifiedTreatment['sub_specialization_slug'];
            }

            $modifiedTreatment['url'] = $url;
            $modifiedTreatments[] = $modifiedTreatment;
        }

        return $modifiedTreatments;
    }

    private function getSearchParams(Request $request, $isAutoComplete = false)
    {
        $parameters = array(
            'destination' => $request->get('destination_id'),
            'treatment' => $request->get('treatment_id'),
            'destinationLabel' => $request->get('sb_destination', ''),
            'treatmentLabel' => $request->get('sb_treatment', ''),
            'filter' => $request->get('filter', '')
        );

        if ($isAutoComplete) {
            $parameters['term'] = $request->get('term');
        }

        return new SearchParameterBag($parameters);
    }
}