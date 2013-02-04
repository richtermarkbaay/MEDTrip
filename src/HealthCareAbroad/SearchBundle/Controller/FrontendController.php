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
                $template = 'SearchBundle:Frontend/Widgets:mainSearchWidget.html.twig';

                break;

            case 'treatments':
                $options['treatmentId'] = $request->get('treatmentId');
                $template = 'SearchBundle:Frontend/Widgets:resultsPageSearchWidget.html.twig';

                if ($request->get('subContext', '') == 'country') {
                    $options['autocompleteRoute'] = 'search_frontend_ajaxLoadCountries';
                } elseif ($request->get('subContext') == 'city') {
                    $options['autocompleteRoute'] = 'search_frontend_ajaxLoadCities';
                }

                break;

            case 'combination':
                //unused
                $options['destinationId'] = $request->get('destinationId');
                $options['destinationLabel'] = $request->get('destinationLabel');
                $options['treatmentId'] = $request->get('treatmentId');
                $options['treatmentLabel'] = $request->get('treatmentLabel');

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
        $searchParams = $this->getSearchParams($request);
        $sessionVariables = array();

        switch ($searchParams->get('context')) {
            case SearchParameterBag::SEARCH_TYPE_DESTINATIONS:
                $routeParameters['country'] = $this->getDoctrine()->getEntityManager()->getRepository('HelperBundle:Country')->find($searchParams->get('countryId'))->getSlug();
                $route = 'search_frontend_results_countries';
                $sessionVariables['countryId'] = $searchParams->get('countryId');

                if ($searchParams->get('cityId')) {
                    $routeParameters['city'] = $this->getDoctrine()->getEntityManager()->getRepository('HelperBundle:City')->find($searchParams->get('cityId'))->getSlug();
                    $route = 'search_frontend_results_cities';
                    $sessionVariables['cityId'] = $searchParams->get('cityId');
                }

                break;

            case SearchParameterBag::SEARCH_TYPE_TREATMENTS:
                $termDocuments = $this->get('services.search')->getTermDocuments($searchParams);

                if (count($termDocuments) == 1) {
                    $termDocument = $termDocuments[0];
                    $routeParameters['specialization'] = $this->getDoctrine()->getEntityManager()->getRepository('TreatmentBundle:Specialization')->find($termDocument['specialization_id'])->getSlug();
                    $route = 'search_frontend_results_specializations';
                    $sessionVariables['specializationId'] = $termDocument['specialization_id'];

                    if ($termDocument['treatment_id']) {
                        $routeParameters['treatment'] = $this->getDoctrine()->getEntityManager()->getRepository('TreatmentBundle:Treatment')->find($termDocument['treatment_id'])->getSlug();
                        $route = 'search_frontend_results_treatments';
                        $sessionVariables['treatmentId'] = $termDocument['treatment_id'];
                    } elseif ($termDocument['sub_specialization_id']) {
                        $routeParameters['subSpecialization'] = $this->getDoctrine()->getEntityManager()->getRepository('TreatmentBundle:SubSpecialization')->find($termDocument['sub_specialization_id'])->getSlug();
                        $route = 'search_frontend_results_subSpecializations';
                        $sessionVariables['subSpecializationId'] = $termDocument['sub_specialization_id'];
                    }
                } elseif ($termDocuments) {
                    throw new NotFoundHttpException('No implementation yet');
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
                    $url = $this->buildUrl($termDocument);

                } elseif ($termDocuments) {
                    throw NotFoundHttpException('No implementation yet');
                } else {
                    throw NotFoundHttpException();
                }

                //This code is needed when working in dev environment
                if (get_class($this->container) === 'appDevDebugProjectContainer') {
                    $url = '/app_dev.php'.$url;
                }
                //will be used by our router listener
                $request->getSession()->set(md5($url), json_encode($this->transformParams($termDocument)));

                return $this->redirect($url);

            default:

                return new RedirectResponse($request->headers->get('referer'));
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
            'routeName' => 'search_frontend_results_countries',
            'paginationParameters' => array('country' => $country->getSlug()),
            'destinationId' => $country->getId() . '-0'
        );
        list($parameters['topSpecializations'], $parameters['topTreatments']) =
            $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionSpecialization')->getCountryTopTreatments($country);

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
            'routeName' => 'search_frontend_results_cities',
            'paginationParameters' => array('city' => $city->getSlug(), 'country' => $city->getCountry()->getSlug()),
            'destinationId' => $city->getCountry()->getId() . '-' . $city->getId()
        );
        list($parameters['topSpecializations'], $parameters['topTreatments']) =
            $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionSpecialization')->getCityTopTreatments($city);

        return $this->render('SearchBundle:Frontend:resultsDestinations.html.twig', $parameters);
    }

    public function searchResultsSpecializationsAction(Request $request)
    {
        $searchTerms = json_decode($request->getSession()->remove('search_terms'), true);

        $specialization = $this->getDoctrine()->getRepository('TreatmentBundle:Specialization')->getSpecialization(isset($searchTerms['specializationId']) ? $searchTerms['specializationId'] : $request->get('specialization'));

        //TODO: This is temporary; use OrmAdapter
        $adapter = new ArrayAdapter($this->get('services.search')->searchBySpecialization($specialization));
        $parameters = array(
            'searchResults' => new Pager($adapter, array('page' => $request->get('page'), 'limit' => $this->resultsPerPage)),
            'searchLabel' => isset($searchTerms['treatmentLabel']) ? $searchTerms['treatmentLabel'] : $specialization->getName(),
            'routeName' => 'search_frontend_results_specializations',
            'paginationParameters' => array('specialization' => $specialization->getSlug()),
            'treatmentId' => $specialization->getId().'-0-0-specialization'
        );
        list($parameters['topCountries'], $parameters['topCities']) =
            $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionSpecialization')->getSpecializationTopDestinations($specialization);

        return $this->render('SearchBundle:Frontend:resultsTreatments.html.twig', $parameters);
    }

    public function searchResultsSubSpecializationsAction(Request $request)
    {
        $searchTerms = json_decode($request->getSession()->remove('search_terms'), true);

        $specialization = $this->getDoctrine()->getRepository('TreatmentBundle:Specialization')->getSpecialization(isset($searchTerms['specializationId']) ? $searchTerms['specializationId'] : $request->get('specialization'));
        $subSpecialization = $this->getDoctrine()->getRepository('TreatmentBundle:SubSpecialization')->getSubSpecialization(isset($searchTerms['subSpecializationId']) ? $searchTerms['subSpecializationId'] : $request->get('subSpecialization'));

        //TODO: This is temporary; use OrmAdapter
        $adapter = new ArrayAdapter($this->get('services.search')->searchBySubSpecialization($searchTerms['subSpecializationId']));
        $parameters = array(
            'searchResults' => new Pager($adapter, array('page' => $request->get('page'), 'limit' => $this->resultsPerPage)),
            'searchLabel' => isset($searchTerms['treatmentLabel']) ? $searchTerms['treatmentLabel'] : $specialization->getName() . ' - ' . $subSpecialization->getName(),
            'routeName' => 'search_frontend_results_subSpecializations',
            'paginationParameters' => array('specialization' => $specialization->getSlug(), 'subSpecialization' => $subSpecialization->getSlug()),
            'treatmentId' => $specialization->getId().'-'.$subSpecialization->getId().'-0-subSpecialization'
        );
        list($parameters['topCountries'], $parameters['topCities']) =
            $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionSpecialization')->getSubSpecializationTopDestinations($subSpecialization);

        return $this->render('SearchBundle:Frontend:resultsTreatments.html.twig', $parameters);
    }

    public function searchResultsTreatmentsAction(Request $request)
    {
        $searchTerms = json_decode($request->getSession()->remove('search_terms'), true);

        $specialization = $this->getDoctrine()->getRepository('TreatmentBundle:Specialization')->getSpecialization(isset($searchTerms['specializationId']) ? $searchTerms['specializationId'] : $request->get('specialization'));
        $treatment = $this->getDoctrine()->getRepository('TreatmentBundle:Treatment')->getTreatment(isset($searchTerms['treatmentId']) ? $searchTerms['treatmentId'] : $request->get('treatment'));

        //TODO: This is temporary; use OrmAdapter
        $adapter = new ArrayAdapter($this->get('services.search')->searchByTreatment($treatment));
        $parameters = array(
            'searchResults' => new Pager($adapter, array('page' => $request->get('page'), 'limit' => $this->resultsPerPage)),
            'searchLabel' => isset($searchTerms['treatmentLabel']) ? $searchTerms['treatmentLabel'] : $specialization->getName() . ' - ' . $treatment->getName(),
            'routeName' => 'search_frontend_results_treatments',
            'paginationParameters' => array('specialization' => $specialization->getSlug(), 'treatment' => $treatment->getSlug()),
            'treatmentId' => $specialization->getId().'-0-'.$treatment->getId().'-treatment'
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

    //TODO: create a dedicated class for this
    private function buildUrl($termDocument)
    {
        $sessionParams = array();

        $combinationPrefix = 'country';

        if ($termDocument['city_id']) {
            $combinationPrefix = 'city';
            $city = $this->getDoctrine()->getRepository('HelperBundle:City')->find($termDocument['city_id']);
        }

        $combinationSuffix = '_specialization';

        if ($termDocument['treatment_id']) {
            $combinationSuffix = '_treatment';
        } elseif ($termDocument['sub_specialization_id']) {
            $combinationSuffix = '_subSpecialization';
        }

        $country = $this->getDoctrine()->getRepository('HelperBundle:Country')->find($termDocument['country_id']);
        $specialization = $this->getDoctrine()->getRepository('TreatmentBundle:Specialization')->find($termDocument['specialization_id']);

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