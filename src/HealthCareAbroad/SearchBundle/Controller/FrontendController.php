<?php
namespace HealthCareAbroad\SearchBundle\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;

use HealthCareAbroad\SearchBundle\Services\SearchParameterBag;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use HealthCareAbroad\HelperBundle\Repository\CountryRepository;
use HealthCareAbroad\HelperBundle\Repository\CityRepository;

/**
 * TODO: Refactor whole class
 *
 */
class FrontendController extends Controller
{
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
        $searchParams = $this->getSearchParams($request);

        $sessionParams = array();
        switch ($searchParams->get('context')) {
            case SearchParameterBag::SEARCH_TYPE_DESTINATIONS:
                $sessionParams['countryId'] = $searchParams->get('countryId');
                if ($searchParams->has('cityId')) {
                    $sessionParams['cityId'] = $searchParams->get('cityId');
                }
                $sessionParams['destinationLabel'] = $request->get('sb_destination');
                $sessionParams['destinationId'] = $request->get('destination_id');
                $route = 'search_frontend_results_destinations';
                break;

            case SearchParameterBag::SEARCH_TYPE_TREATMENTS:
                $sessionParams['specializationId'] = $searchParams->get('specializationId');
                if ($searchParams->has('subSpecializationId')) {
                    $sessionParams['subSpecializationId'] = $searchParams->get('subSpecializationId');
                } else if ($searchParams->has('treatmentId')) {
                    $sessionParams['treatmentId'] = $searchParams->get('treatmentId');
                }
                $sessionParams['treatmentType'] = $searchParams->get('treatmentType');
                $sessionParams['treatmentLabel'] = $request->get('sb_treatment');
                $sessionParams['treatmentId'] = $request->get('treatment_id');
                $route = 'search_frontend_results_treatments';
                break;

            case SearchParameterBag::SEARCH_TYPE_COMBINATION:

                return $this->processCombinationSearch($request, $searchParams);

            default:
                return new RedirectResponse($request->headers->get('referer'));
        }

        $parameters = array();
        $request->getSession()->set('search_terms', json_encode($sessionParams));

        return $this->redirect($this->generateUrl($route, $parameters));
    }

    private function processCombinationSearch(Request $request, SearchParameterBag $searchParams)
    {
        $sessionParams['countryId'] = $searchParams->get('countryId');
        $combinationPrefix = 'country';

        if ($searchParams->has('cityId')) {
            $sessionParams['cityId'] = $searchParams->get('cityId');
            $combinationPrefix = 'city';
        }

        $sessionParams['destinationLabel'] = $request->get('sb_destination');
        $sessionParams['destinationId'] = $request->get('destination_id');

        $locationSlugs = $slugify($sessionParams['destinationLabel'], 'destination');

        $sessionParams['specializationId'] = $searchParams->get('specializationId');
        $combinationSuffix = '_specialization';

        if ($searchParams->has('subSpecializationId')) {
            $sessionParams['subSpecializationId'] = $searchParams->get('subSpecializationId');
            $combinationSuffix = '_subSpecialization';
        } else if ($searchParams->has('treatmentId')) {
            $sessionParams['treatmentId'] = $searchParams->get('treatmentId');
            $combinationSuffix = '_treatment';
        }

        $sessionParams['treatmentType'] = $searchParams->get('treatmentType');
        $sessionParams['treatmentLabel'] = $request->get('sb_treatment');
        $sessionParams['treatmentId'] = $request->get('treatment_id');

        //TODO: make this more robust; also this makes the assumption that each
        //time the label or name is changed for every entity, the slug is also
        //updated.
        $slugify = function($label) {
            return strtolower(trim(preg_replace('/\W+/', '-', $label)));
        };

        $route = null;
        switch ($combinationPrefix . $combinationSuffix) {
            case 'country_specialization':

                break;
            case 'country_subSpecialization':

                break;
            case 'country_treatment':

                break;
            case 'city_specialization':

                break;
            case 'city_subSpecialization':

                break;
            case 'city_treatment':

                break;
        }

        $route = 'search_frontend_results_treatments';
        $route = 'search_frontend_results_destinations';

        $session->set(md5($request->getPathInfo()), json_encode($variables));

        return $this->redirect($url);
    }

    public function searchResultsDestinationsAction(Request $request)
    {
        $country = $city = null;

        //$searchTerms = json_decode($request->getSession()->remove('search_terms'), true);
        $searchTerms = json_decode($request->getSession()->get('search_terms'), true);

        if ($searchTerms['cityId']) {
            if (!$city = $this->getDoctrine()->getEntityManager()->getRepository('HelperBundle:City')->find($searchTerms['cityId'])) {
                throw new \Exception('City not found');
            }
            $country = $city->getCountry();

            $searchResults = $this->get('services.search')->searchByCity($city);

        } else if ($searchTerms['countryId']){
            if (!$country = $this->getDoctrine()->getEntityManager()->getRepository('HelperBundle:Country')->find($searchTerms['countryId'])) {
                throw new \Exception('Country not found');
            }

            $searchResults = $this->get('services.search')->searchByCountry($country);
        }

        //TODO: find a better way to get environment; on the other hand this is
        //sort of a hack and may be temporaray; we may not have to resort to this
        //if an alternative solution is found.
        $urlPrefix = (get_class($this->container) === 'appDevDebugProjectContainer' ? '/app_dev.php/' : '/') . $country->getSlug();
        if ($city) {
            $urlPrefix .= '/' . $city->getSlug();
        }

        $parameters = array(
            'searchResults' => $searchResults,
            'urlPrefix' => $urlPrefix,
            'destinationLabel' => $searchTerms['destinationLabel'],
            'destinationId' => $searchTerms['destinationId']
        );

        list($parameters['topSpecializations'], $parameters['topTreatments']) = array();

        return $this->render('SearchBundle:Frontend:resultsDestinations.html.twig', $parameters);
    }

    public function searchResultsTreatmentsAction(Request $request)
    {
        //$searchTerms = json_decode($request->getSession()->remove('search_terms'), true);
        $searchTerms = json_decode($request->getSession()->get('search_terms'), true);

        switch ($searchTerms['treatmentType']) {
            case 'treatment':
                list($searchResults, $treatment) = $this->get('services.search')->searchByTreatment($searchTerms['treatmentId']);
                $specialization = $this->getDoctrine()->getRepository('TreatmentBundle:Specialization')->find($searchTerms['specializationId']);
                break;
            case 'subSpecialization':
                $searchResults = $this->get('services.search')->searchBySubSpecialization($searchTerms['subSpecializationId']);
                $subSpecialization = $this->getDoctrine()->getRepository('TreatmentBundle:SubSpecialization')->find($searchTerms['subSpecializationId']);
                $specialization = $this->getDoctrine()->getRepository('TreatmentBundle:Specialization')->find($searchTerms['specializationId']);
                break;
            case 'specialization':
                list($searchResults, $specialization) = $this->get('services.search')->searchBySpecialization($searchTerms['specializationId']);
                break;
        }

        $urlPrefix = (get_class($this->container) === 'appDevDebugProjectContainer' ? '/app_dev.php/' : '/') . 'COUNTRY_HERE';

        $parameters = array(
            'searchResults' => $searchResults,
            'specialization' => $specialization,
            'urlPrefix' =>$urlPrefix,
            'treatmentLabel' => $searchTerms['treatmentLabel'],
            'treatmentId' => $searchTerms['treatmentId']
        );

        //TODO: get top countries and cities via ajax
        if (isset($treatment)) {
            $parameters['treatment'] = $treatment;
            list($parameters['topCountries'], $parameters['topCities']) =
                $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionSpecialization')->getTreatmentTopDestinations($treatment);
        } else if (isset($subSpecialization)) {
            $parameters['subSpecialization'] = $subSpecialization;
            list($parameters['topCountries'], $parameters['topCities']) =
                $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionSpecialization')->getSubSpecializationTopDestinations($subSpecialization);
        } else {
            list($parameters['topCountries'], $parameters['topCities']) =
                $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionSpecialization')->getSpecializationTopDestinations($specialization);
        }

        return $this->render('SearchBundle:Frontend:resultsTreatments.html.twig', $parameters);
    }

    public function searchResultsCombinationAction(Request $request)
    {

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
        $context = $searchParams->get('context');

        switch () {
            case SearchParameterBag::SEARCH_TYPE_DESTINATION:
                $parameters = array('country' => $country->getSlug());
                $route = 'search_frontend_results_countries';
                $session->getFlashBag()->set('search_terms', json_encode(array('countryId' => $country->getId())));

                break;

            case '_city':
                $parameters = array('country' => $country->getSlug(), 'city' => $city->getSlug());
                $route = 'search_frontend_results_cities';
                $session->getFlashBag()->set('search_terms', json_encode(array('countryId' => $country->getId(), 'cityId' => $city->getId())));

                break;

            case '_specialization': case '_subSpecialization': //TODO: route for subSpecialization????

                $parameters = array('specialization' => $specialization->getSlug());
                $route = 'search_frontend_results_specializations';

                $session->getFlashBag()->set('search_terms', json_encode(array(
                                'specializationId' => $specialization->getId(),
                )));

                break;

            case '_treatment':
                $parameters = array(
                    'specialization' => $specialization->getSlug(),
                    'treatment' => $treatment->getSlug()
                );
                $route = 'search_frontend_results_treatments';

                $session->getFlashBag()->set('search_terms', json_encode(array(
                                'specializationId' => $specialization->getId(),
                                'treatmentId' => $treatment->getId()
                )));

                break;

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