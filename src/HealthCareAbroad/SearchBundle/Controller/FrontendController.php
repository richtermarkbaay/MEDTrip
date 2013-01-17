<?php
namespace HealthCareAbroad\SearchBundle\Controller;

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

        $options['destinationId'] = '0-0';
        $options['destinationLabel'] = '';
        $options['treatmentId'] = '0-0-0-0';
        $options['treatmentLabel'] = '';

        switch ($options['context']) {
            case 'homepage':
                break;

            case 'destinations':
                $options['destinationId'] = $request->get('destinationId');
                $options['destinationLabel'] = $request->get('destinationLabel');
                break;

            case 'treatments':
                $options['treatmentId'] = $request->get('treatmentId');
                $options['treatmentLabel'] = $request->get('treatmentLabel');
                break;

            case 'combination':
                $options['destinationId'] = $request->get('destinationId');
                $options['destinationLabel'] = $request->get('destinationLabel');
                $options['treatmentId'] = $request->get('treatmentId');
                $options['treatmentLabel'] = $request->get('treatmentLabel');
                break;

            default:
                throw new \Exception('Undefined context');
        }

        return $this->render($template = 'SearchBundle:Frontend:searchWidget.html.twig', array('options' => $options));
    }

    public function searchAction(Request $request)
    {
        $sessionParams = array();

        $searchParams = $this->getSearchParams($request);

        switch ($searchParams->get('context')) {
            case SearchParameterBag::SEARCH_TYPE_DESTINATIONS:
                $sessionParams['countryId'] = $searchParams->get('countryId');
                $country = $this->getDoctrine()->getEntityManager()->getRepository('HelperBundle:Country')->find($searchParams->get('countryId'));
                $parameters = array('country' => $country->getSlug());
                $route = 'search_frontend_results_countries';

                if ($searchParams->get('cityId')) {
                    $sessionParams['cityId'] = $searchParams->get('cityId');
                    $parameters['city'] = $this->getDoctrine()->getEntityManager()->getRepository('HelperBundle:City')->find($searchParams->get('cityId'))->getSlug();
                    $route = 'search_frontend_results_cities';
                }
                $sessionParams['destinationLabel'] = $searchParams->get('destinationLabel');
                $sessionParams['destinationParameter'] = $searchParams->get('destinationParameter');

                break;

            case SearchParameterBag::SEARCH_TYPE_TREATMENTS:
                $sessionParams['specializationId'] = $searchParams->get('specializationId');
                $specialization = $this->getDoctrine()->getEntityManager()->getRepository('TreatmentBundle:Specialization')->find($searchParams->get('specializationId'));
                $parameters = array('specialization' => $specialization->getSlug());
                $route = 'search_frontend_results_specializations';

                if ($searchParams->get('treatmentId')) {
                    $sessionParams['treatmentId'] = $searchParams->get('treatmentId');
                    $treatment = $this->getDoctrine()->getEntityManager()->getRepository('TreatmentBundle:Treatment')->find($searchParams->get('treatmentId'));
                    $parameters['treatment'] = $treatment->getSlug();
                    $route = 'search_frontend_results_treatments';
                } elseif ($searchParams->get('subSpecializationId')) {
                    $sessionParams['subSpecializationId'] = $searchParams->get('subSpecializationId');
                    $subSpecialization = $this->getDoctrine()->getEntityManager()->getRepository('TreatmentBundle:SubSpecialization')->find($searchParams->get('subSpecializationId'));
                    $parameters['subSpecialization'] = $subSpecialization->getSlug();
                    $route = 'search_frontend_results_subSpecializations';
                }
                $sessionParams['treatmentType'] = $searchParams->get('treatmentType');
                $sessionParams['treatmentLabel'] = $searchParams->get('treatmentLabel');
                $sessionParams['treatmentParameter'] = $searchParams->get('treatmentParameter');

                break;

            case SearchParameterBag::SEARCH_TYPE_COMBINATION:
                return $this->processCombinationSearch($request, $searchParams);

            default:
                return new RedirectResponse($request->headers->get('referer'));
        }

        $request->getSession()->set('search_terms', json_encode($sessionParams));

        return $this->redirect($this->generateUrl($route, $parameters));
    }

    private function processCombinationSearch(Request $request, SearchParameterBag $searchParams)
    {
        $sessionParams['countryId'] = $searchParams->get('countryId');
        $combinationPrefix = 'country';

        if ($searchParams->get('cityId')) {
            $sessionParams['cityId'] = $searchParams->get('cityId');
            $combinationPrefix = 'city';

            $city = $this->getDoctrine()->getRepository('HelperBundle:City')->find($searchParams->get('cityId'));
        }

        $sessionParams['destinationLabel'] = $searchParams->get('destinationLabel');
        $sessionParams['destinationParameter'] = $searchParams->get('destinationParameter');
        $sessionParams['specializationId'] = $searchParams->get('specializationId');
        $combinationSuffix = '_specialization';

        if ($searchParams->get('treatmentId')) {
            $sessionParams['treatmentId'] = $searchParams->get('treatmentId');
            $combinationSuffix = '_treatment';
        } elseif ($searchParams->get('subSpecializationId')) {
            $sessionParams['subSpecializationId'] = $searchParams->get('subSpecializationId');
            $combinationSuffix = '_subSpecialization';
        }

        $sessionParams['treatmentType'] = $searchParams->get('treatmentType');
        $sessionParams['treatmentLabel'] = $request->get('sb_treatment');
        $sessionParams['treatmentParameter'] = $request->get('treatment_id');

        $country = $this->getDoctrine()->getRepository('HelperBundle:Country')->find($searchParams->get('countryId'));
        $specialization = $this->getDoctrine()->getRepository('TreatmentBundle:Specialization')->find($searchParams->get('specializationId'));

        switch ($combinationPrefix . $combinationSuffix) {
            case 'country_specialization':
                $url = '/'.$country->getSlug().
                       '/'.$specialization->getSlug();
                break;

            case 'country_subSpecialization':
                $url = '/'.$country->getSlug().
                       '/'.$specialization->getSlug().
                       '/'.$this->getDoctrine()->getRepository('TreatmentBundle:SubSpecialization')->find($searchParams->get('subSpecializationId'))->getSlug();
                break;

            case 'country_treatment':
                $url = '/'.$country->getSlug().
                       '/'.$specialization->getSlug().
                       '/'.$this->getDoctrine()->getRepository('TreatmentBundle:Treatment')->find($searchParams->get('treatmentId'))->getSlug().
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
                       '/'.$this->getDoctrine()->getRepository('TreatmentBundle:SubSpecialization')->find($searchParams->get('subSpecializationId'))->getSlug();
                break;

            case 'city_treatment':
                $url = '/'.$country->getSlug().
                       '/'.$city->getSlug().
                       '/'.$specialization->getSlug().
                       '/'.$this->getDoctrine()->getRepository('TreatmentBundle:Treatment')->find($searchParams->get('treatmentId'))->getSlug().
                       '/treatment';
                break;
        }

        if (get_class($this->container) === 'appDevDebugProjectContainer') {
            $url = '/app_dev.php'.$url;
        }

        $setVariables = function($sessionParams) {
            $variables['countryId'] = $sessionParams['countryId'];
            if (!empty($sessionParams['cityId'])) {
                $variables['cityId'] = $sessionParams['cityId'];
            }
            $variables['specializationId'] = $sessionParams['specializationId'];
            if (!empty($sessionParams['treatmentId'])) {
                $variables['treatmentId'] = $sessionParams['treatmentId'];
            } elseif (!empty($sessionParams['subSpecializationId'])) {
                $variables['subSpecializationId'] = $sessionParams['subSpecializationId'];
            }

            return $variables;
        };

        $session = $request->getSession();
        $session->set(md5($url), json_encode($setVariables($sessionParams)));

        return $this->redirect($url);
    }

    //TODO: combine the two destination-based searches
    public function searchResultsCountriesAction(Request $request)
    {
        $searchTerms = json_decode($request->getSession()->get('search_terms'), true);

        $country = null;
        if (isset($searchTerms['countryId']) && $searchTerms['countryId']) {
            $country = $this->getDoctrine()->getRepository('HelperBundle:Country')->find($searchTerms['countryId']);
        }

        if (!$country) {
            if (!$country = $this->getDoctrine()->getRepository('HelperBundle:Country')->findOneBy(array('slug' => $request->get('country')))) {
                throw new \Exception('No identifier for country');
            }
        }

        $searchResults = $this->get('services.search')->searchByCountry($country);

        //TODO: This is temporary; use OrmAdapter
        $adapter = new ArrayAdapter($searchResults);
        $searchResults = new Pager($adapter, array('page' => $request->get('page'), 'limit' => $this->resultsPerPage));

        $searchLabel = isset($searchTerms['destinationLabel']) ? $searchTerms['destinationLabel'] : $country->getName();

        $parameters = array(
            'searchResults' => $searchResults,
            'searchLabel' => $searchLabel,
            //'destinationParameter' => $searchTerms['destinationParameter'],
            'routeName' => 'search_frontend_results_countries',
            'paginationParameters' => array('country' => $country->getSlug())
        );

        list($parameters['topSpecializations'], $parameters['topTreatments']) =
            $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionSpecialization')->getCountryTopTreatments($country);

        $response = $this->render('SearchBundle:Frontend:resultsDestinations.html.twig', $parameters);

        $cookieName = $request->getPathInfo();
        if (!$request->cookies->has($cookieName)) {
            $cookie = new Cookie($cookieName, md5(json_encode(array('countryId'=> $country->getId()))));
            $response->headers->setCookie($cookie);
        }

        return $response;
    }

    public function searchResultsCitiesAction(Request $request)
    {
        $searchTerms = json_decode($request->getSession()->get('search_terms'), true);

        $city = null;
        if (isset($searchTerms['cityId']) && $searchTerms['cityId'] ) {
            $city = $this->getDoctrine()->getRepository('HelperBundle:City')->find($searchTerms['cityId']);
        }

        if (!$city) {
            if (!$city = $this->getDoctrine()->getRepository('HelperBundle:City')->findOneBy(array('slug' => $request->get('city')))) {
                throw new \Exception('No identifier for city');
            }
        }

        $searchResults = $this->get('services.search')->searchByCity($city);

        //TODO: This is temporary; use OrmAdapter
        $adapter = new ArrayAdapter($searchResults);
        $searchResults = new Pager($adapter, array('page' => $request->get('page'), 'limit' => $this->resultsPerPage));

        $searchLabel = isset($searchTerms['destinationLabel']) ? $searchTerms['destinationLabel'] : $city->getName().', '.$city->getCountry()->getName();

        $parameters = array(
                        'searchResults' => $searchResults,
                        'searchLabel' => $searchLabel,
                        //'destinationParameter' => $searchTerms['destinationParameter'],
                        'routeName' => 'search_frontend_results_cities',
                        'paginationParameters' => array(
                            'city' => $city->getSlug(),
                            'country' => $city->getCountry()->getSlug())
        );

        list($parameters['topSpecializations'], $parameters['topTreatments']) =
            $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionSpecialization')->getCityTopTreatments($city);

        $response = $this->render('SearchBundle:Frontend:resultsDestinations.html.twig', $parameters);

        $cookieName = $request->getPathInfo();
        if (!$request->cookies->has($cookieName)) {
            $cookie = new Cookie($cookieName, md5(json_encode(array('countryId'=> $city->getCountry()->getId(), 'cityId' => $city->getId()))));
            $response->headers->setCookie($cookie);
        }

        return $response;
    }

    //TODO: merge the treatment-based search actions
    public function searchResultsSpecializationsAction(Request $request)
    {
        //$searchTerms = json_decode($request->getSession()->remove('search_terms'), true);
        $searchTerms = json_decode($request->getSession()->get('search_terms'), true);

        $specialization = null;
        if (isset($searchTerms['specializationId']) && $searchTerms['specializationId'] ) {
            $specialization = $this->getDoctrine()->getRepository('HelperBundle:City')->find($searchTerms['specializationId']);
        }

        if (!$specialization) {
            if (!$specialization = $this->getDoctrine()->getRepository('TreatmentBundle:Specialization')->findOneBy(array('slug' => $request->get('specialization')))) {
                throw new \Exception('No identifier for specialization');
            }
        }

        $searchResults = $this->get('services.search')->searchBySpecialization($specialization);

        //TODO: This is temporary; use OrmAdapter
        $adapter = new ArrayAdapter($searchResults);
        $searchResults = new Pager($adapter, array('page' => $request->get('page'), 'limit' => $this->resultsPerPage));

        $searchLabel = isset($searchTerms['treatmentLabel']) ? $searchTerms['treatmentLabel'] : $city->getName().', '.$city->getCountry()->getName();

        $parameters = array(
                        'searchResults' => $searchResults,
                        'searchLabel' => $searchLabel,
                        //'treatmentParameter' => $searchTerms['treatmentParameter'],
                        'routeName' => 'search_frontend_results_specializations',
                        'paginationParameters' => array('specialization' => $specialization->getSlug())
        );

        //TODO: get top countries and cities via ajax
        list($parameters['topCountries'], $parameters['topCities']) =
            $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionSpecialization')->getSpecializationTopDestinations($specialization);

        $response = $this->render('SearchBundle:Frontend:resultsTreatments.html.twig', $parameters);

        $cookieName = $request->getPathInfo();
        if (!$request->cookies->has($cookieName)) {
            $cookie = new Cookie($cookieName, md5(json_encode(array('specializationId'=> $specialization->getId()))));
            $response->headers->setCookie($cookie);
        }

        return $response;
    }

    public function searchResultsSubSpecializationsAction(Request $request)
    {
        //$searchTerms = json_decode($request->getSession()->remove('search_terms'), true);
        $searchTerms = json_decode($request->getSession()->get('search_terms'), true);

        $specialization = null;
        if (isset($searchTerms['specializationId']) && $searchTerms['specializationId'] ) {
            $specialization = $this->getDoctrine()->getRepository('HelperBundle:City')->find($searchTerms['specializationId']);
        }

        if (!$specialization) {
            if (!$specialization = $this->getDoctrine()->getRepository('TreatmentBundle:Specialization')->findOneBy(array('slug' => $request->get('specialization')))) {
                throw new \Exception('No identifier for specialization');
            }
        }

        $subSpecialization = null;
        if (isset($searchTerms['subSpecializationId']) && $searchTerms['subSpecializationId'] ) {
            $subSpecialization = $this->getDoctrine()->getRepository('TreatmentBundle:SubSpecialization')->find($searchTerms['subSpecializationId']);
        }

        if (!$subSpecialization) {
            if (!$subSpecialization = $this->getDoctrine()->getRepository('TreatmentBundle:SubSpecialization')->findOneBy(array('slug' => $request->get('subSpecialization')))) {
                throw new \Exception('No identifier for subSpecialization');
            }
        }

        $searchResults = $this->get('services.search')->searchBySubSpecialization($searchTerms['subSpecializationId']);

        //TODO: This is temporary; use OrmAdapter
        $adapter = new ArrayAdapter($searchResults);
        $searchResults = new Pager($adapter, array('page' => $request->get('page'), 'limit' => $this->resultsPerPage));

        $searchLabel = $searchLabel = isset($searchTerms['treatmentLabel']) ? $searchTerms['treatmentLabel'] : $subSpecialization->getName();

        $parameters = array(
                        'searchResults' => $searchResults,
                        'searchLabel' => $searchLabel,
                        //'treatmentParameter' => $searchTerms['treatmentParameter'],
                        'routeName' => 'search_frontend_results_subSpecializations',
                        'paginationParameters' => array('specialization' => $specialization->getSlug(), 'subSpecialization' => $subSpecialization->getSlug())
        );

        //TODO: get top countries and cities via ajax
        list($parameters['topCountries'], $parameters['topCities']) =
            $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionSpecialization')->getSubSpecializationTopDestinations($subSpecialization);

        $response = $this->render('SearchBundle:Frontend:resultsTreatments.html.twig', $parameters);

        $cookieName = $request->getPathInfo();
        if (!$request->cookies->has($cookieName)) {
            $cookie = new Cookie($cookieName, md5(json_encode(array('specializationId' => $specialization->getId(), 'subSpecializationId' => $subSpecialization->getId()))));
            $response->headers->setCookie($cookie);
        }

        return $response;
    }

    public function searchResultsTreatmentsAction(Request $request)
    {
        //$searchTerms = json_decode($request->getSession()->remove('search_terms'), true);
        $searchTerms = json_decode($request->getSession()->get('search_terms'), true);

        $specialization = null;
        if (isset($searchTerms['specializationId']) && $searchTerms['specializationId'] ) {
            $specialization = $this->getDoctrine()->getRepository('HelperBundle:City')->find($searchTerms['specializationId']);
        }

        if (!$specialization) {
            if (!$specialization = $this->getDoctrine()->getRepository('TreatmentBundle:Specialization')->findOneBy(array('slug' => $request->get('specialization')))) {
                throw new \Exception('No identifier for specialization');
            }
        }

        $treatment = null;
        if (isset($searchTerms['treatmentId']) && $searchTerms['treatmentId']) {
            $treatment = $this->getDoctrine()->getRepository('TreatmentBundle:Treatment')->find($searchTerms['treatmentId']);
        }

        if (!$treatment) {
            if (!$treatment = $this->getDoctrine()->getRepository('TreatmentBundle:Treatment')->findOneBy(array('slug' => $request->get('treatment')))) {
                throw new \Exception('No identifier for treatment');
            }
        }

        $searchResults = $this->get('services.search')->searchByTreatment($treatment);

        //TODO: This is temporary; use OrmAdapter
        $adapter = new ArrayAdapter($searchResults);
        $searchResults = new Pager($adapter, array('page' => $request->get('page'), 'limit' => $this->resultsPerPage));

        $searchLabel = isset($searchTerms['treatmentLabel']) ? $searchTerms['treatmentLabel'] : $treatment->getName();

        $parameters = array(
            'searchResults' => $searchResults,
            'searchLabel' => $searchLabel,
            //'treatmentParameter' => $searchTerms['treatmentParameter'],
            'routeName' => 'search_frontend_results_treatments',
            'paginationParameters' => array('specialization' => $specialization->getSlug(), 'treatment' => $treatment->getSlug())
        );

        list($parameters['topCountries'], $parameters['topCities']) =
            $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionSpecialization')->getTreatmentTopDestinations($treatment);

        return $this->render('SearchBundle:Frontend:resultsTreatments.html.twig', $parameters);
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

    private function getSearchParams(Request $request, $isAutoComplete = false)
    {
        $parameters = array(
            'destination' => $request->get('destination_id'),
            'treatment' => $request->get('treatment_id'),
            'destinationLabel' => $request->get('sb_destination'),
            'treatmentLabel' => $request->get('sb_treatment')
        );

        if ($isAutoComplete) {
            $parameters['term'] = $request->get('term');
        }

        return new SearchParameterBag($parameters);
    }

    /**
     * Comma is used as delimiter
     *
     * @param unknown $term
     * @return multitype:
     */
    private function tokenizeSearchTerm($term)
    {
        $tokens = array();

        if (!empty($term)) {
            $tokens = array_filter(array_map('trim', explode(',', $term)));
        }

        return $tokens;
    }

    private function appendCountryUrls($countries, $treatment)
    {
        $center = $treatment->getMedicalCenter();

        $modifiedCountries = array();
        foreach ($countries as $country) {
            $modifiedCountry['id'] = $country['id'];
            $modifiedCountry['name'] = $country['name'];
            $modifiedCountry['url'] = '/'.$country['name'].'/'.$center->getSlug().'/'.$treatment->getSlug();

            $modifiedCountries[] = $modifiedCountry;
        }

        return $modifiedCountries;
    }

    /*
            case '_country_treatment':

                $variables = array(
                    'countryId'	=> $country->getId(),
                    'treatmentId' => $treatment->getId(),
                );

                $url = '/'.$country->getSlug().'/'.$medicalCenter->getSlug().'/'.$treatment->getSlug();

                //TODO: this can produce a url with two differing content: one
                //with data specific to procedure and the other the more general
                //treatment.
                if ($searchTerms['procedureId']) {
                    $variables['procedureId'] = $searchTerms['procedureId'];
                    $url .= '?procedureId='.$searchTerms['procedureId'];
                }

                $session->set(md5($request->getPathInfo()), json_encode($variables));

                //TODO: make a generator function or class

                return $this->redirect($url);

                break;//safeguard against removal of return statement

            case '_city_treatment':
                $variables = array(
                    'countryId' => $country->getId(),
                    'cityId' => $city->getId(),
                    'treatmentId' => $treatment->getId(),

                );

                $url = '/'.$country->getSlug().'/'.$city->getSlug().'/'.$medicalCenter->getSlug().'/'.$treatment->getSlug();

                if ($searchTerms['procedureId']) {
                    $variables['procedureId'] = $searchTerms['procedureId'];
                    $url .= '?procedureId='.$searchTerms['procedureId'];
                }

                $session->set(md5($request->getPathInfo()), json_encode($variables));

                return $this->redirect($url);

                break;//safeguard against removal of return statement

            default:
                throw new \Exception('Invalid search term/s'. $searchParams->get('context'));
        }
     */
}