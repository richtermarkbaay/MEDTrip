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
        $searchParams = $this->getSearchParams($request);

        switch ($searchParams->get('context')) {
            case SearchParameterBag::SEARCH_TYPE_DESTINATIONS:
                $parameters['country'] = $this->getDoctrine()->getEntityManager()->getRepository('HelperBundle:Country')->find($searchParams->get('countryId'))->getSlug();
                $route = 'search_frontend_results_countries';

                if ($searchParams->get('cityId')) {
                    $parameters['city'] = $this->getDoctrine()->getEntityManager()->getRepository('HelperBundle:City')->find($searchParams->get('cityId'))->getSlug();
                    $route = 'search_frontend_results_cities';
                }

                break;

            case SearchParameterBag::SEARCH_TYPE_TREATMENTS:
                $parameters['specialization'] = $this->getDoctrine()->getEntityManager()->getRepository('TreatmentBundle:Specialization')->find($searchParams->get('specializationId'))->getSlug();
                $route = 'search_frontend_results_specializations';

                if ($searchParams->get('treatmentId')) {
                    $parameters['treatment'] = $this->getDoctrine()->getEntityManager()->getRepository('TreatmentBundle:Treatment')->find($searchParams->get('treatmentId'))->getSlug();
                    $route = 'search_frontend_results_treatments';
                } elseif ($searchParams->get('subSpecializationId')) {
                    $parameters['subSpecialization'] = $this->getDoctrine()->getEntityManager()->getRepository('TreatmentBundle:SubSpecialization')->find($searchParams->get('subSpecializationId'))->getSlug();
                    $route = 'search_frontend_results_subSpecializations';
                }

                break;

            case SearchParameterBag::SEARCH_TYPE_COMBINATION:
                //we don't have configured routes for this type of search so we need to generate the url manually
                $url = $this->buildUrl($searchParams);

                //This code is needed when working in dev environment
                if (get_class($this->container) === 'appDevDebugProjectContainer') {
                    $url = '/app_dev.php'.$url;
                }
                //will be used by our router listener
                $request->getSession()->set(md5($url), json_encode($searchParams->getDynamicRouteParams()));

                return $this->redirect($url);

            default:

                return new RedirectResponse($request->headers->get('referer'));
        }

        // this is used to avoid using slugs after redirection
        $request->getSession()->set('search_terms', json_encode($searchParams->all()));

        return $this->redirect($this->generateUrl($route, $parameters));
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
            'paginationParameters' => array('country' => $country->getSlug())
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
            'paginationParameters' => array('city' => $city->getSlug(), 'country' => $city->getCountry()->getSlug())
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
            'paginationParameters' => array('specialization' => $specialization->getSlug())
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
            'paginationParameters' => array('specialization' => $specialization->getSlug(), 'subSpecialization' => $subSpecialization->getSlug())
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

    //TODO: create a dedicated class for this
    private function buildUrl(SearchParameterBag $searchParams)
    {
        $combinationPrefix = 'country';

        if ($searchParams->get('cityId')) {
            $combinationPrefix = 'city';
            $city = $this->getDoctrine()->getRepository('HelperBundle:City')->find($searchParams->get('cityId'));
        }

        $combinationSuffix = '_specialization';

        if ($searchParams->get('treatmentId')) {
            $combinationSuffix = '_treatment';
        } elseif ($searchParams->get('subSpecializationId')) {
            $combinationSuffix = '_subSpecialization';
        }

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

        return $url;
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
}