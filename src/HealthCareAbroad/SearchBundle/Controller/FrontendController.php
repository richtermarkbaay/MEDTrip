<?php
namespace HealthCareAbroad\SearchBundle\Controller;

use HealthCareAbroad\HelperBundle\Services\PageMetaConfigurationService;

use HealthCareAbroad\SearchBundle\Services\SearchUrlGenerator;

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
use HealthCareAbroad\FrontendBundle\Controller\ResponseHeadersController;

/**
 * TODO: Refactor whole class
 *
 */
class FrontendController extends ResponseHeadersController
{
    private $resultsPerPage = 20;

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
                $template = 'SearchBundle:Frontend/Widgets:newSearchWidgetHomepage.html.twig';
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
     * ProcessSearchListener will direct us to this action if and only if keywords
     * are present in the form submission
     *
     * TODO: direct user to specific action if only one term document is matched
     *
     * @param Request $request
     */
    public function searchProcessKeywordsAction(Request $request)
    {
        $routeParameters = array();
        $sessionVariables = array();

        $filters = array(
            'treatmentName' => $request->get('sb_treatment'),
            'destinationName' => $request->get('sb_destination'),
            'treatmentId' => $request->get('treatment_id'),
            'countryId' => 0,
            'cityId' => 0
        );

        if ($destinationId = $request->get('destination_id')) {
            list ($countryId, $cityId) = explode('-', $destinationId);

            $filters['countryId'] = $countryId;
            $filters['cityId'] = $cityId;
        }

        $searchTerms = $this->get('services.search')->getSearchTermsWithUniqueDocumentsFilteredOn($filters);

        $context = $request->attributes->get('context');

        if (count($searchTerms) == 1 || $context === 'destination') {
            $searchTerm = $searchTerms[0];

            $routeConfig = $this->get('services.search')->getRouteConfig($searchTerm, $this->get('doctrine'), $context);

            // this is used to avoid using slugs after redirection
            $request->getSession()->set('search_terms', json_encode($routeConfig['sessionParameters']));

            return $this->redirect($this->generateUrl($routeConfig['routeName'], $routeConfig['routeParameters']));
        }

        $termIds = array();
        foreach ($searchTerms as $term) {
            $termIds[] = (int) $term['term_id'];
        }
        $uniqueTermIds = array_flip(array_flip($termIds));

        $routeConfig = $this->get('services.search')->getRouteConfigFromFilters($filters, $this->get('doctrine'), $uniqueTermIds);

        // this is used to avoid using slugs after redirection
        $request->getSession()->set('search_terms', json_encode($routeConfig['sessionParameters']));

        return $this->redirect($this->generateUrl($routeConfig['routeName'], $routeConfig['routeParameters']));
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
        if ((isset($searchParameters['sub-specialization']) || isset($searchParameters['sub_specialization'])) && !isset($searchParameters['subSpecialization'])) {
            if (isset($searchParameters['sub-specialization'])) {
                $searchParameters['subSpecialization'] = $searchParameters['sub-specialization'];
            } elseif (isset($searchParameters['sub_specialization'])) {
                $searchParameters['subSpecialization'] = $searchParameters['sub_specialization'];
            }
        }

        //FIXME: this is just a patch to support combined country and city dropwdown in narrow search
        // This patch is also present in SearchService::loadSuggestions
        if (isset($searchParameters['destinations']) && $searchParameters['destinations']) {
            list($searchParameters['country'], $searchParameters['city']) = explode('-', $searchParameters['destinations']);
            if ((int) $searchParameters['city'] == 0) {
                unset($searchParameters['city']);
            }
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

            //COMBINED SEARCH
            //This is for cases when treatment/subspecialization is not known at the time of template's rendering but added by user
            //through the search form.
            if (isset($searchParameters['treatment'])) {
                $treatment = $this->getDoctrine()->getEntityManager()->getRepository('TreatmentBundle:Treatment')->find($searchParameters['treatment']);
                $routeParameters['treatment'] = $treatment->getSlug();
                $routeParameters['specialization'] = $treatment->getSpecialization()->getSlug();
                $sessionVariables['treatmentId'] = $treatment->getId();
                $sessionVariables['specializationId'] = $treatment->getSpecialization()->getId();

                if (isset($searchParameters['city'])) {
                    $route = 'frontend_search_combined_countries_cities_specializations_treatments';
                } else {
                    $route = 'frontend_search_combined_countries_specializations_treatments';
                }
            } elseif (isset($searchParameters['subSpecialization'])) {
                $subSpecialization = $this->getDoctrine()->getEntityManager()->getRepository('TreatmentBundle:SubSpecialization')->find($searchParameters['subSpecialization']);
                $routeParameters['subSpecializations'] = $subSpecialization->getSlug();
                $routeParameters['specialization'] = $subSpecialization->getSpecialization()->getSlug();
                $sessionVariables['subSpecializationId'] = $subSpecialization->getId();
                $sessionVariables['specializationId'] = $subSpecialization->getSpecialization()->getId();

                if (isset($searchParameters['city'])) {
                    $route = 'frontend_search_combined_countries_cities_specializations__subSpecializations';
                } else {
                    $route = 'frontend_search_combined_countries_specializations__subSpecializations';
                }
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
                    //Unreachable code?
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
                    $route = 'frontend_search_results_related_terms';
                    $sessionVariables = array('termIds' => array($term['id']));
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
                    $route = 'frontend_search_results_related_terms';
                    $sessionVariables = array('termIds' => array($term['id']));

                    if ($searchParams->get('countryId')) {
                        $routeParameters['country'] = $this->getDoctrine()->getEntityManager()->getRepository('HelperBundle:Country')->find($searchParams->get('countryId'))->getSlug();
                        $route = 'frontend_search_results_related_terms_country';
                        $sessionVariables['countryId'] = $searchParams->get('countryId');

                        if ($searchParams->get('cityId')) {
                            $routeParameters['city'] = $this->getDoctrine()->getEntityManager()->getRepository('HelperBundle:City')->find($searchParams->get('cityId'))->getSlug();
                            $route = 'frontend_search_results_related_terms_city';
                            $sessionVariables['cityId'] = $searchParams->get('cityId');
                        }
                    }

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
        $pager = new Pager($adapter, array('page' => $request->get('page'), 'limit' => $this->resultsPerPage));
        $parameters = array(
            'searchResults' => $pager,
            'searchLabel' => isset($searchTerms['destinationLabel']) ? $searchTerms['destinationLabel'] : $country->getName(),
            'routeName' => 'frontend_search_results_countries',
            'paginationParameters' => array('country' => $country->getSlug()),
            'destinationId' => $country->getId() . '-0',
            'country' => $country,
            'includedNarrowSearchWidgets' => array('specialization', 'destinations'),
            'narrowSearchParameters' => array(SearchParameterBag::FILTER_COUNTRY => $country->getId()),
            'featuredClinicParams' => array('countryId' => $country->getId())
        );

        // set total results for page metas
        $request->attributes->set('pageMetaVariables', array(PageMetaConfigurationService::CLINIC_RESULTS_COUNT_VARIABLE => $pager->getTotalResults()));
        $request->attributes->set('searchObjects', array(SearchUrlGenerator::SEARCH_URL_PARAMETER_COUNTRY => $country));
        $this->setBreadcrumbRequestAttributes($request, array('country' => $country));

        return  $this->setResponseHeaders($this->render('SearchBundle:Frontend:resultsDestinations.html.twig', $parameters));
    }

    public function searchResultsCitiesAction(Request $request)
    {
        $searchTerms = json_decode($request->getSession()->remove('search_terms'), true);

        if (!$city = $this->getDoctrine()->getRepository('HelperBundle:City')->getCity(isset($searchTerms['cityId']) ? $searchTerms['cityId'] : $request->get('city'))) {
            throw new NotFoundHttpException();
        }

        //TODO: This is temporary; use OrmAdapter
        $adapter = new ArrayAdapter($this->get('services.search')->searchByCity($city));
        $pager = new Pager($adapter, array('page' => $request->get('page'), 'limit' => $this->resultsPerPage));
        $parameters = array(
            'searchResults' => $pager,
            'searchLabel' => isset($searchTerms['destinationLabel']) ? $searchTerms['destinationLabel'] : $city->getName().', '.$city->getCountry()->getName(),
            'routeName' => 'frontend_search_results_cities',
            'paginationParameters' => array('city' => $city->getSlug(), 'country' => $city->getCountry()->getSlug()),
            'destinationId' => $city->getCountry()->getId() . '-' . $city->getId(),
            'city' => $city,
            'country' => $city->getCountry(),
            'includedNarrowSearchWidgets' => array('specialization'),
            'narrowSearchParameters' => array(SearchParameterBag::FILTER_COUNTRY => $city->getCountry()->getId(), SearchParameterBag::FILTER_CITY => $city->getId()),
            'featuredClinicParams' => array('cityId' => $city->getId())
        );

        // set total results for page metas
        $request->attributes->set('pageMetaVariables', array(PageMetaConfigurationService::CLINIC_RESULTS_COUNT_VARIABLE => $pager->getTotalResults()));
        $request->attributes->set('searchObjects', array(
            SearchUrlGenerator::SEARCH_URL_PARAMETER_COUNTRY => $city->getCountry(),
            SearchUrlGenerator::SEARCH_URL_PARAMETER_CITY => $city
        ));
        $this->setBreadcrumbRequestAttributes($request, array('country' => $city->getCountry(), 'city' => $city));

        return $this->setResponseHeaders($this->render('SearchBundle:Frontend:resultsDestinations.html.twig', $parameters));
    }

    public function searchResultsSpecializationsAction(Request $request)
    {
        $searchTerms = json_decode($request->getSession()->remove('search_terms'), true);

        if (!$specialization = $this->getDoctrine()->getRepository('TreatmentBundle:Specialization')->getSpecialization(
                        isset($searchTerms['specializationId']) ? $searchTerms['specializationId'] : $request->get('specialization'),
                        true)) {
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
        $pager = new Pager($adapter, array('page' => $request->get('page'), 'limit' => $this->resultsPerPage));
        $parameters = array(
            'searchResults' => $pager,
            'searchLabel' => isset($searchTerms['treatmentLabel']) ? $searchTerms['treatmentLabel'] : $specialization->getName(),
            'routeName' => 'frontend_search_results_specializations',
            'paginationParameters' => array('specialization' => $specialization->getSlug()),
            'treatmentId' => $termId,
            'specialization' => $specialization,
            'includedNarrowSearchWidgets' => array('sub_specialization', 'destinations'),
            'narrowSearchParameters' => array(SearchParameterBag::FILTER_SPECIALIZATION => $specialization->getId()),
            'featuredClinicParams' => array('specializationId' => $specialization->getId())
        );

        // set total results for page metas
        $request->attributes->set('pageMetaVariables', array(PageMetaConfigurationService::CLINIC_RESULTS_COUNT_VARIABLE => $pager->getTotalResults()));
        $request->attributes->set('searchObjects', array(
            SearchUrlGenerator::SEARCH_URL_PARAMETER_SPECIALIZATION => $specialization,
        ));
        $this->setBreadcrumbRequestAttributes($request, array('specialization' => $specialization));

        return $this->setResponseHeaders($this->render('SearchBundle:Frontend:resultsTreatments.html.twig', $parameters));
    }

    public function searchResultsSubSpecializationsAction(Request $request)
    {
        $searchTerms = json_decode($request->getSession()->remove('search_terms'), true);

        if (!$specialization = $this->getDoctrine()->getRepository('TreatmentBundle:Specialization')->getSpecialization(
                        isset($searchTerms['specializationId']) ? $searchTerms['specializationId'] : $request->get('specialization'),
                        true)) {
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
        $pager = new Pager($adapter, array('page' => $request->get('page'), 'limit' => $this->resultsPerPage));
        $parameters = array(
            'searchResults' => $pager,
            'searchLabel' => isset($searchTerms['treatmentLabel']) ? $searchTerms['treatmentLabel'] : $request->get('specialization') . ' - ' . $request->get('subSpecialization'),
            'routeName' => 'frontend_search_results_subSpecializations',
            'paginationParameters' => $paginationParameters,
            'treatmentId' => $termId,
            'specialization' => $specialization,
            'subSpecialization' => $subSpecialization,
            'includedNarrowSearchWidgets' => array('treatment', 'destinations'),
            'narrowSearchParameters' => array(SearchParameterBag::FILTER_SPECIALIZATION => $specialization->getId(), SearchParameterBag::FILTER_SUBSPECIALIZATION => $subSpecialization->getId()),
            'featuredClinicParams' => array('subSpecializationId' => $subSpecialization->getId())
        );

        // set total results for page metas
        $request->attributes->set('pageMetaVariables', array(PageMetaConfigurationService::CLINIC_RESULTS_COUNT_VARIABLE => $pager->getTotalResults()));
        $request->attributes->set('searchObjects', array(
            SearchUrlGenerator::SEARCH_URL_PARAMETER_SPECIALIZATION => $specialization,
            SearchUrlGenerator::SEARCH_URL_PARAMETER_SUB_SPECIALIZATION => $subSpecialization,
        ));
        $this->setBreadcrumbRequestAttributes($request, array('specialization' => $specialization, 'subSpecialization' => $subSpecialization));

        return $this->setResponseHeaders($this->render('SearchBundle:Frontend:resultsTreatments.html.twig', $parameters));
    }

    public function searchResultsTreatmentsAction(Request $request)
    {
        $searchTerms = json_decode($request->getSession()->remove('search_terms'), true);
        if (!$specialization = $this->getDoctrine()->getRepository('TreatmentBundle:Specialization')->getSpecialization(
                        isset($searchTerms['specializationId']) ? $searchTerms['specializationId'] : $request->get('specialization'),
                        true)) {
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
        $pager = new Pager($adapter, array('page' => $request->get('page'), 'limit' => $this->resultsPerPage));
        $parameters = array(
            'searchResults' => $pager,
            'searchLabel' => isset($searchTerms['treatmentLabel']) ? $searchTerms['treatmentLabel'] : $specialization->getName() . ' - ' . $treatment->getName(),
            'routeName' => 'frontend_search_results_treatments',
            'paginationParameters' => array('specialization' => $specialization->getSlug(), 'treatment' => $treatment->getSlug()),
            'treatmentId' => $termId,
            'treatment' => $treatment,
            'includedNarrowSearchWidgets' => array('destinations'),
            'narrowSearchParameters' => $treatment ? array(SearchParameterBag::FILTER_SPECIALIZATION => $specialization->getId(), SearchParameterBag::FILTER_TREATMENT => $treatment->getId()) : array(),
            'featuredClinicParams' => array('treatmentId' => $treatment->getId())
        );

        // set total results for page metas
        $request->attributes->set('pageMetaVariables', array(PageMetaConfigurationService::CLINIC_RESULTS_COUNT_VARIABLE => $pager->getTotalResults()));
        $request->attributes->set('searchObjects', array(
            SearchUrlGenerator::SEARCH_URL_PARAMETER_SPECIALIZATION => $specialization,
            SearchUrlGenerator::SEARCH_URL_PARAMETER_TREATMENT => $treatment,
        ));
        $this->setBreadcrumbRequestAttributes($request, array('specialization' => $specialization, 'treatment' => $treatment));

        return $this->setResponseHeaders($this->render('SearchBundle:Frontend:resultsTreatments.html.twig', $parameters));
    }

    public function searchResultsRelatedAction(Request $request)
    {
        $searchTerms = json_decode($request->getSession()->remove('search_terms'), true);

        $paginationParameters = array('tag' => $request->get('tag', ''));
        $context = SearchParameterBag::SEARCH_TYPE_TREATMENTS;

        if ($request->get('country', '')) {
            $paginationParameters['country'] = $request->get('country', '');
            $context = SearchParameterBag::SEARCH_TYPE_COMBINATION;
        }
        if ($request->get('city', '')) {
            $paginationParameters['city'] = $request->get('city', '');
            $context = SearchParameterBag::SEARCH_TYPE_COMBINATION;
        }

        if (empty($searchTerms)) {
            // If session does not exist the tag can either be a partially or fully formed slug
            $filters = array('treatmentSlug' => $request->get('tag', ''));

            // We can be sure that the slugs for destination are the fully-formed one
            // as we are only allowing destination searches if the id is used.
            if ($countrySlug = $request->get('country', '')) {
                if (!$country = $this->getDoctrine()->getRepository('HelperBundle:Country')->getCountry($countrySlug)) {
                    throw new NotFoundHttpException();
                }
                $filters['countryId'] = $country->getId();
            }
            if ($citySlug = $request->get('city', '')) {
                if (!$city = $this->getDoctrine()->getRepository('HelperBundle:City')->getCity($citySlug)) {
                    throw new NotFoundHttpException();
                }
                $filters['countryId'] = $city->getCountry()->getId();
                $filters['cityId'] = $city->getId();
            }

            $searchTerms = $this->get('services.search')->getSearchTermsWithUniqueDocumentsFilteredOn($filters);

            if (empty($searchTerms)) {
                return $this->render('SearchBundle:Frontend:noResults.html.twig', array(
                                'searchLabel' => $request->get('tag', ''),
                                'specializations' => $this->getDoctrine()->getRepository('TermBundle:SearchTerm')->findAllActiveTermsGroupedBySpecialization()
                ));
            }

            $termIds = array();
            foreach ($searchTerms as $term) {
                $termIds[] = (int) $term['term_id'];
            }
            $uniqueTermIds = array_flip(array_flip($termIds));

            $routeConfig = $this->get('services.search')->getRouteConfigFromFilters($filters, $this->getDoctrine(), $uniqueTermIds);

            //$paginationParameters = $routeConfig['routeParameters'];
            $searchTerms = $routeConfig['sessionParameters'];
            //$request->getSession()->set('search_terms', json_encode($searchTerms));
        }

        //FIXME: we just show Home -> Related Search for now
        //$this->setBreadcrumbRequestAttributesForRelatedSearch($request, $searchTerms);

        //TODO: This is temporary; use OrmAdapter
        $adapter = new ArrayAdapter($this->get('services.search')->searchByTerms($searchTerms));
        $searchResults = new Pager($adapter, array('page' => $request->get('page'), 'limit' => $this->resultsPerPage));

        if ($searchResults->count()) {
            $response = $this->render('SearchBundle:Frontend:resultsSectioned.html.twig', array(
                'searchResults' => $searchResults,
                'searchLabel' => $request->get('tag', ''),
                'routeName' => $request->attributes->get('_route'),
                'paginationParameters' => $paginationParameters,
                'relatedTreatments' => $this->get('services.search')->getRelatedTreatments($searchTerms)
            ));

            $response = $this->setResponseHeaders($response);
        } else {
            $response = $this->render('SearchBundle:Frontend:noResults.html.twig', array(
                'searchResults' => $searchResults,
                'searchLabel' => $request->get('tag', ''),
                'specializations' => $this->getDoctrine()->getRepository('TermBundle:SearchTerm')->findAllActiveTermsGroupedBySpecialization()
            ));
        }

        return $response;
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

    //TODO: move to service layer
    private function setBreadcrumbRequestAttributes(Request $request, array $attributes)
    {
        foreach ($attributes as $key => $value) {
            if (is_object($value)) {
                $request->attributes->set($key, array('name' => $value->getName(), 'slug' => $value->getSlug()));
            } elseif (is_array($value)) {
                $request->attributes->set($key, $value);
            }
        }
    }

    private function setBreadcrumbRequestAttributesForRelatedSearch(Request $request, array $searchTerms)
    {
        //$attributes['country'] = $request->get('country', '');
        $attributes['tag'] = $request->get('tag', '');

        // The route may already have route parameters named country and city so we set different attribute names
        if (isset($searchTerms['cityId'])) {
            $attributes['cityAttrib'] = $this->getDoctrine()->getRepository('HelperBundle:City')->find($searchTerms['cityId']);
            $attributes['countryAttrib'] = $attributes['cityAttrib']->getCountry();
        } elseif (isset($searchTerms['countryId'])) {
            $attributes['countryAttrib'] = $this->getDoctrine()->getRepository('HelperBundle:Country')->find($searchTerms['countryId']);
        }

        $this->setBreadcrumbRequestAttributes($request, $attributes);
    }
}