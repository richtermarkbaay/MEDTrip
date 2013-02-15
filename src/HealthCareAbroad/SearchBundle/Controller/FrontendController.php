<?php
namespace HealthCareAbroad\SearchBundle\Controller;

use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Gedmo\Sluggable\Util\Urlizer;
use HealthCareAbroad\HelperBundle\Entity\City;
use HealthCareAbroad\HelperBundle\Entity\Country;
use HealthCareAbroad\HelperBundle\Repository\CountryRepository;
use HealthCareAbroad\HelperBundle\Repository\CityRepository;
use HealthCareAbroad\PagerBundle\Pager;
use HealthCareAbroad\PagerBundle\Adapter\ArrayAdapter;
use HealthCareAbroad\SearchBundle\Services\SearchParameterBag;
use HealthCareAbroad\TermBundle\Entity\TermDocument;

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
            case 'main':
                $template = 'SearchBundle:Frontend/Widgets:searchWidgetMain.html.twig';
                break;

            case 'homepage':
                $template = 'SearchBundle:Frontend/Widgets:searchWidgetHomepage.html.twig';
                break;

            case 'sidebar':
                $template = 'SearchBundle:Frontend/Widgets:searchWidgetSidebar.html.twig';
                break;

            case 'destinations':
                $options['destinationId'] = $request->get('destinationId');
                $template = 'SearchBundle:Frontend/Widgets:searchWidgetResultsPage.html.twig';

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
                $template = 'SearchBundle:Frontend/Widgets:searchWidgetResultsPage.html.twig';

                if ($request->get('subContext', '') == 'country') {
                    $options['autocompleteRoute'] = 'frontend_search_ajaxLoadCountries';
                } elseif ($request->get('subContext') == 'city') {
                    $options['autocompleteRoute'] = 'frontend_search_ajaxLoadCities';
                }

                break;

            default:
                throw new \Exception('Undefined context');
        }

        return $this->render($template, array('options' => $options));
    }

    /**
     * Search page
     *
     * @param Request $request
     */
    public function searchAction(Request $request)
    {
        $parameters = array();

        $parameters['topDestinations'] = $this->getDoctrine()->getRepository('TermBundle:SearchTerm')->getTopCountries();
        $parameters['topTreatments'] = $this->getDoctrine()->getRepository('TermBundle:SearchTerm')->getTopTreatments();

        return  $this->render('SearchBundle:Frontend:search.html.twig', $parameters);
    }

    /**
     * ProcessSearchListener will direct us to this action if only keywords are present in the form submission
     *
     * TODO: direct user to specific action if only one term document is matched
     *
     * @param Request $request
     */
    public function searchProcessKeywordsAction(Request $request)
    {
        $termDocuments = $this->get('services.search')->getTermDocumentsFilteredOn(array(
                        'treatmentName' => $request->get('sb_treatment'),
                        'destinationName' => $request->get('sb_destination')
        ));

        $termIds = array();
        foreach ($termDocuments as $doc) {
            $termIds[] = $doc['term_id'];
        }
        $uniqueTermIds = array_flip(array_flip($termIds));

        $keywords = array();
        $keywordsRouteParam = '';

        if ($request->get('sb_treatment')) {
            $keywords['treatmentName'] = $request->get('sb_treatment');
            $keywordsRouteParam = Urlizer::urlize($request->get('sb_treatment'));
        }
        if ($request->get('sb_destination')) {
            $keywords['destinationName'] = $request->get('sb_destination');
            $keywordsRouteParam = $keywordsRouteParam ? $keywordsRouteParam . '-' . Urlizer::urlize($request->get('sb_destination')) : Urlizer::urlize($request->get('sb_destination'));
        }

        $routeParameters['keywords'] = $keywordsRouteParam;
        $route = 'frontend_search_results_keywords';
        $sessionVariables = array('termIds' => $uniqueTermIds, 'keywords' => $keywords);

        // this is used to avoid using slugs after redirection
        $request->getSession()->set('search_terms', json_encode($sessionVariables));

        return $this->redirect($this->generateUrl($route, $routeParameters));
    }

    /**
     * ProcessSearchListener will direct us to this action if a form variable named
     * searchParameter is present.
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function searchProcessNarrowAction(Request $request)
    {
        $requestParams = $request->request->all();

        $searchParameters = $requestParams['searchParameter'];

        //FIXME: to be compatible with the search widget which uses a dash
        if (isset($searchParameters['sub-specialization']) && !isset($searchParameters['subSpecialization'])) {
            $searchParameters['subSpecialization'] = $searchParameters['sub-specialization'];
        }

        if (isset($searchParameters['specialization']) && isset($searchParameters['country'])) {

            $combinationPrefix = 'country';
            $country = $this->getDoctrine()->getRepository('HelperBundle:Country')->find($searchParameters['country']);

            if (isset($searchParameters['city'])) {
                $combinationPrefix = 'city';
                $city = $this->getDoctrine()->getRepository('HelperBundle:City')->find($searchParameters['city']);
                if (!$country) {
                    $country = $city->getCountry();
                }
            }

            $combinationSuffix = '_specialization';
            $specialization = $this->getDoctrine()->getRepository('TreatmentBundle:Specialization')->find($searchParameters['specialization']);

            if (isset($searchParameters['treatment'])) {
                $combinationSuffix = '_treatment';
            } elseif (isset($searchParameters['subSpecialization'])) {
                $combinationSuffix = '_subSpecialization';
            }

            $url = '';
            switch ($combinationPrefix . $combinationSuffix) {
                case 'country_specialization':
                    $url = '/'.$country->getSlug().
                    '/'.$specialization->getSlug();
                    break;

                case 'country_subSpecialization':
                    $url = '/'.$country->getSlug().
                    '/'.$specialization->getSlug().
                    '/'.$this->getDoctrine()->getRepository('TreatmentBundle:SubSpecialization')->find($searchParameters['subSpecialization'])->getSlug();
                    break;

                case 'country_treatment':
                    $url = '/'.$country->getSlug().
                    '/'.$specialization->getSlug().
                    '/'.$this->getDoctrine()->getRepository('TreatmentBundle:Treatment')->find($searchParameters['treatment'])->getSlug().
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
                    '/'.$this->getDoctrine()->getRepository('TreatmentBundle:SubSpecialization')->find($searchParameters['subSpecialization'])->getSlug();
                    break;

                case 'city_treatment':
                    $url = '/'.$country->getSlug().
                    '/'.$city->getSlug().
                    '/'.$specialization->getSlug().
                    '/'.$this->getDoctrine()->getRepository('TreatmentBundle:Treatment')->find($searchParameters['treatment'])->getSlug().
                    '/treatment';
                    break;
            }

            $url = $this->getPrefix().$url;

            //will be used by our router listener
            //$request->getSession()->set(md5($url), json_encode($this->transformParams($termDocument)));

            return $this->redirect($url);

        } elseif (isset($searchParameters['country'])) {
            $routeParameters['country'] = $this->getDoctrine()->getEntityManager()->getRepository('HelperBundle:Country')->find($searchParameters['country'])->getSlug();
            $route = 'frontend_search_results_countries';
            $sessionVariables['countryId'] = $searchParameters['country'];

            if (isset($searchParameters['city'])) {
                $routeParameters['city'] = $this->getDoctrine()->getEntityManager()->getRepository('HelperBundle:City')->find($searchParameters['city'])->getSlug();
                $route = 'frontend_search_results_cities';
                $sessionVariables['cityId'] = $searchParameters['city'];
            }
        } elseif (isset($searchParameters['specialization'])) {
            $routeParameters['specialization'] = $this->getDoctrine()->getEntityManager()->getRepository('TreatmentBundle:Specialization')->find($searchParameters['specialization'])->getSlug();
            $route = 'frontend_search_results_specializations';
            $sessionVariables['specializationId'] = $searchParameters['specialization'];

            if (isset($searchParameters['treatment'])) {
                $routeParameters['treatment'] = $this->getDoctrine()->getEntityManager()->getRepository('TreatmentBundle:Treatment')->find($searchParameters['treatment'])->getSlug();
                $route = 'frontend_search_results_treatments';
                $sessionVariables['treatmentId'] = $searchParameters['treatment'];
            } elseif (isset($searchParameters['subSpecialization'])) {
                $routeParameters['subSpecialization'] = $this->getDoctrine()->getEntityManager()->getRepository('TreatmentBundle:SubSpecialization')->find($searchParameters['subSpecialization'])->getSlug();
                $route = 'frontend_search_results_subSpecializations';
                $sessionVariables['subSpecializationId'] = $searchParameters['subSpecialization'];
            }
        }

        // this is used to avoid using slugs after redirection
        $request->getSession()->set('search_terms', json_encode($sessionVariables));

        return $this->redirect($this->generateUrl($route, $routeParameters));
    }

    /**
     * TODO:
     * 1. refactor
     * 2. refactor
     * 3. refactor
     * 4. and refactor!
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function searchProcessAction(Request $request)
    {
        $sessionVariables = array();

        $searchParams = $this->getSearchParams($request, true);

        switch ($searchParams->get('context')) {
            case SearchParameterBag::SEARCH_TYPE_DESTINATIONS:
                if ($searchParams->get('countryId')) {
                    $routeParameters['country'] = $this->getDoctrine()->getEntityManager()->getRepository('HelperBundle:Country')->find($searchParams->get('countryId'))->getSlug();
                    $route = 'frontend_search_results_countries';
                    $sessionVariables['countryId'] = $searchParams->get('countryId');

                    if ($searchParams->get('cityId')) {
                        $routeParameters['city'] = $this->getDoctrine()->getEntityManager()->getRepository('HelperBundle:City')->find($searchParams->get('cityId'))->getSlug();
                        $route = 'frontend_search_results_cities';
                        $sessionVariables['cityId'] = $searchParams->get('cityId');
                    }
                } elseif ($searchParams->get('destinationLabel')) {
                    $termDocuments = $this->get('services.search')->getTermDocumentsByDestination($searchParams);
                }

                break;

            case SearchParameterBag::SEARCH_TYPE_TREATMENTS:
                $termDocuments = $this->get('services.search')->getTermDocuments($searchParams);

                if (count($termDocuments) == 1) {
                    $termDocument = $termDocuments[0];
                    $routeParameters['specialization'] = $this->getDoctrine()->getEntityManager()->getRepository('TreatmentBundle:Specialization')->find($termDocument['specialization_id'])->getSlug();
                    $route = 'frontend_search_results_specializations';
                    $sessionVariables = array('specializationId' => $termDocument['specialization_id'], 'termId' => $termDocument['term_id']);

                    switch ($termDocument['type']) {
                        case TermDocument::TYPE_TREATMENT:
                            $routeParameters['treatment'] = $this->getDoctrine()->getEntityManager()->getRepository('TreatmentBundle:Treatment')->find($termDocument['document_id'])->getSlug();
                            $route = 'frontend_search_results_treatments';
                            $sessionVariables['treatmentId'] = $termDocument['treatment_id'];
                            break;
                        case TermDocument::TYPE_SUBSPECIALIZATION:
                            $routeParameters['subSpecialization'] = $this->getDoctrine()->getEntityManager()->getRepository('TreatmentBundle:SubSpecialization')->find($termDocument['document_id'])->getSlug();
                            $route = 'frontend_search_results_subSpecializations';
                            $sessionVariables['subSpecializationId'] = $termDocument['sub_specialization_id'];
                            break;
                    }

                } elseif ($termDocuments) {
                    $term = $this->get('services.search')->getTerm($searchParams->get('treatmentId'));

                    $routeParameters = array('tag' => $term['slug']);
                    $route = 'frontend_search_results_related';
                    $sessionVariables = array('termId' => $term['id']);
                } else {
                    //TODO: no results found
                    throw new NotFoundHttpException();
                }

                break;

            case SearchParameterBag::SEARCH_TYPE_COMBINATION:

                if ($searchParams->get('treatmentId')) {
                    $termDocuments = $this->get('services.search')->getTermDocuments($searchParams);
                } else {
                    $termDocuments = $this->get('services.search')->getTermDocumentsByTermName($searchParams);
                }

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

                    return $this->redirect($url);

                } elseif ($termDocuments) {
                    $term = $this->get('services.search')->getTerm($searchParams->get('treatmentId'));

                    $routeParameters = array('tag' => $term['slug']);
                    $route = 'frontend_search_results_related';
                    $sessionVariables = array('termId' => $term['id']);

                } else {
                    //TODO: no results found
                    throw new NotFoundHttpException();
                }

                break;

            default:
                throw new NotFoundHttpException();
        }

        // this is used to avoid using slugs after redirection
        $request->getSession()->set('search_terms', json_encode($sessionVariables));

        return $this->redirect($this->generateUrl($route, $routeParameters));
    }

    public function searchResultsCountriesAction(Request $request)
    {
        $searchTerms = json_decode($request->getSession()->remove('search_terms'), true);

        if (!$country = $this->getDoctrine()->getRepository('HelperBundle:Country')->getCountry(isset($searchTerms['countryId']) ? $searchTerms['countryId'] : $request->get('country'))) {
           //todo: no results
            throw new NotFoundHttpException();
        }

        //TODO: This is temporary; use OrmAdapter
        $adapter = new ArrayAdapter($this->get('services.search')->searchByCountry($country));
        $parameters = array(
            'searchResults' => new Pager($adapter, array('page' => $request->get('page'), 'limit' => $this->resultsPerPage)),
            'searchLabel' => isset($searchTerms['destinationLabel']) ? $searchTerms['destinationLabel'] : $country->getName(),
            'routeName' => 'frontend_search_results_countries',
            'paginationParameters' => array('country' => $country->getSlug()),
            'destinationId' => $country->getId() . '-0',
            'country' => $country,
            'includedNarrowSearchWidgets' => array('specialization', 'sub_specialization', 'treatment', 'city'),
            'narrowSearchParameters' => array(SearchParameterBag::FILTER_COUNTRY => $country->getId())
        );

        return  $this->render('SearchBundle:Frontend:resultsDestinations.html.twig', $parameters);
    }

    public function searchResultsCitiesAction(Request $request)
    {
        $searchTerms = json_decode($request->getSession()->remove('search_terms'), true);

        if (!$city = $this->getDoctrine()->getRepository('HelperBundle:City')->getCity(isset($searchTerms['cityId']) ? $searchTerms['cityId'] : $request->get('city'))) {
            throw new NotFoundHttpException();
        }

        //TODO: This is temporary; use OrmAdapter
        $adapter = new ArrayAdapter($this->get('services.search')->searchByCity($city));
        $parameters = array(
            'searchResults' => new Pager($adapter, array('page' => $request->get('page'), 'limit' => $this->resultsPerPage)),
            'searchLabel' => isset($searchTerms['destinationLabel']) ? $searchTerms['destinationLabel'] : $city->getName().', '.$city->getCountry()->getName(),
            'routeName' => 'frontend_search_results_cities',
            'paginationParameters' => array('city' => $city->getSlug(), 'country' => $city->getCountry()->getSlug()),
            'destinationId' => $city->getCountry()->getId() . '-' . $city->getId(),
            'city' => $city,
            'country' => $city->getCountry(),
            'includedNarrowSearchWidgets' => array('specialization', 'sub_specialization', 'treatment'),
            'narrowSearchParameters' => array(SearchParameterBag::FILTER_COUNTRY => $city->getCountry()->getId(), SearchParameterBag::FILTER_CITY => $city->getId())
        );

        return $this->render('SearchBundle:Frontend:resultsDestinations.html.twig', $parameters);
    }

    public function searchResultsSpecializationsAction(Request $request)
    {
        $searchTerms = json_decode($request->getSession()->remove('search_terms'), true);

        if (!$specialization = $this->getDoctrine()->getRepository('TreatmentBundle:Specialization')->getSpecialization(isset($searchTerms['specializationId']) ? $searchTerms['specializationId'] : $request->get('specialization'))) {
            throw new NotFoundHttpException();
        }

        //TODO: verify if we still need this snippet
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
            'specialization' => $specialization,
            'includedNarrowSearchWidgets' => array('sub_specialization', 'treatment', 'country', 'city'),
            'narrowSearchParameters' => array(SearchParameterBag::FILTER_SPECIALIZATION => $specialization->getId())
        );

        return $this->render('SearchBundle:Frontend:resultsTreatments.html.twig', $parameters);
    }

    public function searchResultsSubSpecializationsAction(Request $request)
    {
        $searchTerms = json_decode($request->getSession()->remove('search_terms'), true);

        if (!$specialization = $this->getDoctrine()->getRepository('TreatmentBundle:Specialization')->getSpecialization(isset($searchTerms['specializationId']) ? $searchTerms['specializationId'] : $request->get('specialization'))) {
            throw new NotFoundHttpException();
        }
        if (!$subSpecialization = $this->getDoctrine()->getRepository('TreatmentBundle:SubSpecialization')->getSubSpecialization(isset($searchTerms['subSpecializationId']) ? $searchTerms['subSpecializationId'] : $request->get('subSpecialization'))) {
            throw new NotFoundHttpException();
        }

        //TODO: verify if we still need this snippet
        if (isset($searchTerms['termId'])) {
            $termId = $searchTerms['termId'];
        } else {
            $term = $this->get('services.search')->getTerm($request->get('subSpecialization'), array('column' => 'slug'));
            $termId = $term['id'];
        }

        //TODO: This is temporary; use OrmAdapter
        $adapter = new ArrayAdapter($this->get('services.search')->searchBySubSpecialization($subSpecialization));
        $paginationParameters = array('specialization' => $specialization->getSlug(), 'subSpecialization' => $subSpecialization->getSlug());

        $parameters = array(
            'searchResults' => new Pager($adapter, array('page' => $request->get('page'), 'limit' => $this->resultsPerPage)),
            'searchLabel' => isset($searchTerms['treatmentLabel']) ? $searchTerms['treatmentLabel'] : $request->get('specialization') . ' - ' . $request->get('subSpecialization'),
            'routeName' => 'frontend_search_results_subSpecializations',
            'paginationParameters' => $paginationParameters,
            'treatmentId' => $termId,
            'specialization' => $specialization,
            'subSpecialization' => $subSpecialization,
            'includedNarrowSearchWidgets' => array('treatment', 'country', 'city'),
            'narrowSearchParameters' => array(SearchParameterBag::FILTER_SPECIALIZATION => $specialization->getId(), SearchParameterBag::FILTER_SUBSPECIALIZATION => $subSpecialization->getId())
        );

        return $this->render('SearchBundle:Frontend:resultsTreatments.html.twig', $parameters);
    }

    public function searchResultsTreatmentsAction(Request $request)
    {
        $searchTerms = json_decode($request->getSession()->remove('search_terms'), true);

        if (!$specialization = $this->getDoctrine()->getRepository('TreatmentBundle:Specialization')->getSpecialization(isset($searchTerms['specializationId']) ? $searchTerms['specializationId'] : $request->get('specialization'))) {
            throw new NotFoundHttpException();
        }
        if (!$treatment = $this->getDoctrine()->getRepository('TreatmentBundle:Treatment')->getTreatment(isset($searchTerms['treatmentId']) ? $searchTerms['treatmentId'] : $request->get('treatment'))) {
            throw new NotFoundHttpException();
        }

        //TODO: verify if we still need this snippet
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
            'treatment' => $treatment,
            'includedNarrowSearchWidgets' => array('country', 'city'),
            'narrowSearchParameters' => $treatment ? array(SearchParameterBag::FILTER_SPECIALIZATION => $specialization->getId(), SearchParameterBag::FILTER_TREATMENT => $treatment->getId()) : array()
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
                        'searchResults' => new Pager($adapter, array('page' => $request->get('page'), 'limit' => $this->resultsPerPage)),
                        'searchLabel' => $request->get('tag', ''),
                        'routeName' => 'frontend_search_results_related',
                        'paginationParameters' => array('tag' => $request->get('tag', '')),
                        'relatedTreatments' => $this->get('services.search')->getRelatedTreatments($term['id'])
        ));
    }

    /**
     * TODO: redirect requests that did not originally come from a form submission
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function searchResultsKeywordsAction(Request $request)
    {
        $searchTerms = json_decode($request->getSession()->get('search_terms'), true);
        $termIds = $searchTerms['termIds'];

        $searchLabel = '';
        $filters = array();
        if ($termIds) {
            if (isset($searchTerms['keywords']['treatmentName']) && isset($searchTerms['keywords']['destinationName'])) {
                $searchLabel = $searchTerms['keywords']['treatmentName'] . ' and ' . $searchTerms['keywords']['destinationName'];
                //$filters['treatmentName'] = $searchTerms['keywords']['treatmentName'];
                $filters['destinationName'] = $searchTerms['keywords']['destinationName'];
            } elseif (isset($searchTerms['keywords']['treatmentName'])) {
                $searchLabel = $searchTerms['keywords']['treatmentName'];
                //$filters['treatmentName'] = $searchTerms['keywords']['treatmentName'];
            } elseif (isset($searchTerms['keywords']['destinationName'])) {
                $searchLabel = $searchTerms['keywords']['destinationName'];
                $filters['destinationName'] = $searchTerms['keywords']['destinationName'];
            }
        }

        //TODO: This is temporary; use OrmAdapter
        $searchResults = array();
        if ($termIds) {
            $searchResults = $this->get('services.search')->searchByTerms($searchTerms['termIds'], $filters);
        }

        $adapter = new ArrayAdapter($searchResults);

        return $this->render('SearchBundle:Frontend:resultsKeywords.html.twig', array(
                        'searchResults' => new Pager($adapter, array('page' => $request->get('page'), 'limit' => $this->resultsPerPage)),
                        'searchLabel' => $searchLabel,
                        'routeName' => 'frontend_search_results_keywords',
                        'paginationParameters' => array('keywords' => $request->get('keywords'))
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

    /**
     * AJAX handler for narrow search results widget
     *
     * @param Request $request
     */
    public function ajaxLoadNarrowSearchAction(Request $request)
    {
        $results = $this->get('services.search')->loadSuggestions($request->request->all());

        return new Response(\json_encode($results), 200, array('content-type' => 'application/json'));
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


        switch ($termDocument['type']) {
            case TermDocument::TYPE_TREATMENT:
                $combinationSuffix = '_treatment';
                break;
            case TermDocument::TYPE_SUBSPECIALIZATION:
                $combinationSuffix = '_subSpecialization';
                break;
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

    private function getPrefix()
    {
        $prefix = '';
        if (get_class($this->container) === 'appDevDebugProjectContainer') {
            $prefix = '/app_dev.php';
        }
        return $prefix;
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
}