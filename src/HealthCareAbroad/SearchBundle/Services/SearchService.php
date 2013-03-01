<?php
namespace HealthCareAbroad\SearchBundle\Services;
use HealthCareAbroad\TermBundle\Entity\TermDocument;

use HealthCareAbroad\SearchBundle\Services\SearchStrategy\DefaultSearchStrategy;

use Symfony\Component\DependencyInjection\ContainerInterface;
use HealthCareAbroad\SearchBundle\Services\SearchStrategy\SearchStrategy;
use HealthCareAbroad\SearchBundle\Services\SearchParameterBag;
use Doctrine\ORM\EntityManager;

/**
 * SearchService
 *
 * Long description here.
 *
 * @author Harold Modesto <harold.modesto@chromedia.com>
 *
 */
class SearchService
{
    /**
     *
     * @var SearchStrategy
     */
    private $searchStrategy;

    /**
     * Constructor
     *
     * @param SearchStrategy $searchStrategy Strategy class to use
     */
    public function __construct(SearchStrategy $searchStrategy)
    {
        $this->searchStrategy = $searchStrategy;
    }

    /**
     * TODO: rename to loadDestinations
     *
     * @param SearchParameterBag $searchParams Strategy class to use
     *
     * @return Ambigous <multitype:, multitype:multitype:string  >
     */
    public function getDestinations(SearchParameterBag $searchParams)
    {
//         $this->searchStrategy->setResultType(SearchStrategy::RESULT_TYPE_ARRAY);

//         return $this
//             ->transformResults($this->searchStrategy->search($searchParams));

        return $this->searchStrategy->getDestinationsByName($searchParams);
    }

    public function getAllDestinations()
    {
        return $this->searchStrategy->getAllDestinations();
    }

    /**
     *  TODO: rename to loadTreatments
     *
     * @param SearchParameterBag $searchParams Strategy class to use
     *
     * @return Ambigous <multitype:, multitype:multitype:string  >
     */
    public function getTreatments(SearchParameterBag $searchParams)
    {
        return $this->searchStrategy->getTreatmentsByName($searchParams);
    }

    public function getAllTreatments()
    {
        return $this->searchStrategy->getAllTreatments();
    }

    public function loadSuggestions($parameters)
    {
        switch ($parameters['filter']) {
            case 'country';
                $results = $this->searchStrategy->loadCountries($parameters);
                break;

            case 'city';
                $results = $this->searchStrategy->loadCities($parameters);
                break;

            case 'treatment':
                $results = $this->searchStrategy->loadTreatments($parameters);
                break;

            case 'subSpecialization':
            case 'sub-specialization':
                $results = $this->searchStrategy->loadSubSpecializations($parameters);
                break;

            case 'specialization':
                $results = $this->searchStrategy->loadSpecializations($parameters);
                break;

            default:
        }

        return $results;
    }

    public function getSearchTermsWithUniqueDocumentsFilteredOn(array $filters)
    {
        return $this->searchStrategy->getSearchTermsFilteredOn($filters, true);
    }

    public function getSearchTermsFilteredOn(array $filters)
    {
        return $this->searchStrategy->getSearchTermsFilteredOn($filters);
    }

    public function getTermDocumentsByTermName($searchParams)
    {
        return $this->searchStrategy->getTermDocumentsByTermName($searchParams);
    }

    public function getTermDocuments(SearchParameterBag $searchParams)
    {
        $filters = array();

        if ($searchParams->get('countryId', 0)) {
            $filters['country_id'] = $searchParams->get('countryId');
        }

        if ($searchParams->get('cityId', 0)) {
            $filters['city_id'] = $searchParams->get('cityId');
        }

        return $this->searchStrategy
            ->getTermDocuments($searchParams, array('filters' => $filters));
    }

    private function transformResults(array $results)
    {
        if ($this->searchStrategy->isViewReadyResults()) {
            return $results;
        }

        //TODO: implementation
        $transformedResults = $results;

        return $transformedResults;
    }

    public function searchByCountry($country)
    {
        return $this->searchStrategy->searchInstitutionsByCountry($country);
    }

    public function searchByCity($city)
    {
        return $this->searchStrategy->searchInstitutionsByCity($city);
    }

    public function searchBySpecialization($specialization)
    {
        return $this->searchStrategy
            ->searchMedicalCentersBySpecialization($specialization);
    }

    public function searchBySubSpecialization($subSpecialization)
    {
        return $this->searchStrategy
            ->searchMedicalCentersBySubSpecialization($subSpecialization);
    }

    public function searchByTreatment($treatment)
    {
        return $this->searchStrategy
            ->searchMedicalCentersByTreatment($treatment);
    }

    /**
     * Search by tag/term
     *
     * @param unknown $tag
     */
    public function searchByTag($tag)
    {
        return $this->searchStrategy->searchMedicalCentersByTerm($tag);
    }

    /**
     * Search by terms
     *
     * @param array $termIds
     */
    public function searchByTerms(array $searchTerms = array(), array $filters = array())
    {
        if (!isset($searchTerms['termIds']) || empty($searchTerms['termIds'])) {
            if (!isset($searchTerms['treatmentName']) || empty($searchTerms['treatmentName'])) {
                return array();
            }
        }

        $termIds = $searchTerms['termIds'];
        $filters = array();

        if (isset($searchTerms['countryId']) && $searchTerms['countryId']) {
            $filters['countryId'] = $searchTerms['countryId'];
        }

        if (isset($searchTerms['cityId']) && $searchTerms['cityId']) {
            $filters['cityId'] = $searchTerms['cityId'];
        }


        return $this->searchStrategy->searchMedicalCentersByTerms($termIds, $filters);
    }

    public function getMedicalCentersByTerm($term, $type = null)
    {
        return $this->searchStrategy->getMedicalCentersByTerm($term, $type);
    }

    public function getTerm($value, $options = array())
    {
        return $this->searchStrategy->getTerm($value, $options);
    }

    public function getTerms(array $termIds, $options)
    {
        if (empty($termIds)) {
            return array();
        }

        return $this->searchStrategy->getTerms($termIds, $options);
    }

    public function getRelatedTreatments(array $searchTerms)
    {
        $categorized = array();

        $termIds = isset($searchTerms['termIds']) ? $searchTerms['termIds'] : array();
        $convertedIds = array();
        foreach ($termIds as $id) {
            $convertedIds[] = (int) $id;
        }
        $filters = array();
        if (isset($searchTerms['countryId'])) {
            $filters['countryId'] = $searchTerms['countryId'];
        }
        if (isset($searchTerms['cityId'])) {
            $filters['cityId'] = $searchTerms['cityId'];
        }

        $results = $this->searchStrategy->getRelatedTreatments($convertedIds, $filters);
        $countItems = count($results);

        //TODO: merge/optimize loops
        $sId = 0;
        $loopCounter = 1;
        //group this now by sub specialty so we don't have problems rendering in grouped by subs
        
        // this will be in a format of
        // [specialization_id] = {'id': _id, 'name': _name, '', sub_specializations: {} }
        $groupedBySubSpecializations = array();
        foreach ($results as $row) {
            if (!isset($groupedBySubSpecializations[$row['specialization_id']])) {
                $groupedBySubSpecializations[$row['specialization_id']] = array(
                    'id' => $row['specialization_id'],
                    'name' => $row['specialization_name'],
                    'slug' => $row['specialization_slug'],
                    'sub_specializations' => array()
                );
            }
            $currentSpecializationData = &$groupedBySubSpecializations[$row['specialization_id']];
            $subSpecializationId = $row['sub_specialization_id'] ? $row['sub_specialization_id'] : 0;
            if (!isset($currentSpecializationData['sub_specializations'][$subSpecializationId])) {
                $currentSpecializationData['sub_specializations'][$subSpecializationId] = $subSpecializationId
                    ? array(
                        'subSpecializationId' => $row['sub_specialization_id'],
                        'subSpecializationName' => $row['sub_specialization_name'],
                        'subSpecializationSlug' => $row['sub_specialization_slug']
                    )
                    : array(
                        'subSpecializationId' => 0,
                        'subSpecializationName' => 'Other Treatments',
                        'subSpecializationSlug' => ''
                    );
                $currentSpecializationData['sub_specializations'][$subSpecializationId]['treatments'] = array();
            }
            // set the treatments
            if ($row['treatment_id']) {
                $currentSpecializationData['sub_specializations'][$subSpecializationId]['treatments'][$row['treatment_id']] = array(
                    'treatmentId' => $row['treatment_id'],
                    'treatmentName' => $row['treatment_name'],
                    'treatmentSlug' => $row['treatment_slug']
                );
            }
            
        }
        
        return $groupedBySubSpecializations;
        /**
        foreach ($results as $row) {
            if ($sId != $row['specialization_id']) {
                if ($sId != 0) {
                    $specialization['subCategories'] = $temps;
                    $categorized[] = $specialization;
                    $temps = array();
                }

                $specialization = array();
                $specialization['id'] = $row['specialization_id'];
                $specialization['name'] = $row['specialization_name'];
                $specialization['slug'] = $row['specialization_slug'];
//                 $specialization['subSpecialization'] = array();
//                 $specialization['treatments'] = array();

                $sId = $row['specialization_id'];
            }

            $temp = array();
            if (isset($row['sub_specialization_id'])) {
                $temp['subSpecializationId'] = $row['sub_specialization_id'];
                $temp['subSpecializationName'] = $row['sub_specialization_name'];
                $temp['subSpecializationSlug'] = $row['sub_specialization_slug'];
            }

            if (isset($row['treatment_id'])) {
                $temp['treatmentId'] = $row['treatment_id'];
                $temp['treatmentName'] = $row['treatment_name'];
                $temp['treatmentSlug'] = $row['treatment_slug'];
            }

            $temps[] = $temp;

            if ($loopCounter++ == $countItems) {
                $specialization['subCategories'] = $temps;
                $categorized[] = $specialization;
                $temps = array();
            }
        }
        **/

        return $categorized;
    }

    public function getRouteConfig($parameters, $doctrine, $context = '')
    {
        $routeName = '';

        switch ($context) {
            case 'combined':
                $routeName = 'frontend_search_combined_countries_';
                $countryId = $parameters['country_id'];

                if (isset($parameters['city_id']) && $parameters['city_id']) {
                    $routeName .= 'cities_';
                    $cityId = $parameters['city_id'];
                }

                switch ($parameters['type']) {
                    case TermDocument::TYPE_SPECIALIZATION:
                        $routeName .= 'specializations';
                        $specializationId = $parameters['specialization_id'];
                        break;
                    case TermDocument::TYPE_SUBSPECIALIZATION:
                        $routeName .= 'specializations__subSpecializations';
                        $specializationId = $parameters['specialization_id'];
                        $subSpecializationId = $parameters['sub_specialization_id'];
                        break;
                    case TermDocument::TYPE_TREATMENT:
                        $routeName .= 'specializations_treatments';
                        $specializationId = $parameters['specialization_id'];
                        $treatmentId = $parameters['treatment_id'];
                        break;
                }

                break;

            case 'destination':
                $routeName = 'frontend_search_results_countries';
                $countryId = $parameters['country_id'];

                if (isset($parameters['city_id']) && $parameters['city_id']) {
                    $routeName = 'frontend_search_results_cities';
                    $cityId = $parameters['city_id'];
                }

                break;

            case 'treatment':
                switch ($parameters['type']) {
                    case TermDocument::TYPE_SPECIALIZATION:
                        $routeName = 'frontend_search_results_specializations';
                        $specializationId = $parameters['specialization_id'];
                        break;
                    case TermDocument::TYPE_SUBSPECIALIZATION:
                        $routeName = 'frontend_search_results_subSpecializations';
                        $specializationId = $parameters['specialization_id'];
                        $subSpecializationId = $parameters['sub_specialization_id'];
                        break;
                    case TermDocument::TYPE_TREATMENT:
                        $routeName = 'frontend_search_results_treatments';
                        $specializationId = $parameters['specialization_id'];
                        $treatmentId = $parameters['treatment_id'];
                        break;
                }

                break;

            default:
        }

        $routeParameters = array();
        $sessionParameters = array();

        if (isset($countryId)) {
            $sessionParameters['countryId'] = $countryId;
            $routeParameters['country'] = $doctrine->getRepository('HelperBundle:Country')->find($countryId)->getSlug();
        }
        if (isset($cityId)) {
            $sessionParameters['cityId'] = $cityId;
            $routeParameters['city'] = $doctrine->getRepository('HelperBundle:City')->find($cityId)->getSlug();
        }
        if (isset($specializationId)) {
            $sessionParameters['specializationId'] = $specializationId;
            $routeParameters['specialization'] = $doctrine->getRepository('TreatmentBundle:Specialization')->find($specializationId)->getSlug();
        }
        if (isset($subSpecializationId)) {
            $sessionParameters['subSpecializationId'] = $subSpecializationId;
            $routeParameters['subSpecialization'] = $doctrine->getRepository('TreatmentBundle:SubSpecialization')->find($subSpecializationId)->getSlug();
        }
        if (isset($treatmentId)) {
            $sessionParameters['treatmentId'] = $treatmentId;
            $routeParameters['treatment'] = $doctrine->getRepository('TreatmentBundle:Treatment')->find($treatmentId)->getSlug();
        }

        return array(
            'routeName' => $routeName,
            'routeParameters' => $routeParameters,
            'sessionParameters' => $sessionParameters
        );
    }

    /*
     * At this moment the logic inside this function is only applicable for keyword searches
     */
    public function getRouteConfigFromFilters($filters, $doctrine, $uniqueTermIds)
    {
        $routeName = 'frontend_search_results_related_terms';
        $routeParameters = array('tag' => $filters['treatmentName']);
        $sessionParameters = array('termIds' => $uniqueTermIds);

        if (isset($filters['countryId']) && $filters['countryId']) {
            $routeName = 'frontend_search_results_related_terms_country';
            $routeParameters['country'] = $doctrine->getRepository('HelperBundle:Country')->find($filters['countryId'])->getSlug();
            $sessionParameters['countryId'] = $filters['countryId'];
        }

        if (isset($filters['cityId']) && $filters['cityId']) {
            $routeName = 'frontend_search_results_related_terms_city';
            $routeParameters['city'] = $doctrine->getRepository('HelperBundle:City')->find($filters['cityId'])->getSlug();
            $sessionParameters['cityId'] = $filters['cityId'];
        }

        return array(
            'routeName' => $routeName,
            'routeParameters' => $routeParameters,
            'sessionParameters' => $sessionParameters
        );
    }
}
