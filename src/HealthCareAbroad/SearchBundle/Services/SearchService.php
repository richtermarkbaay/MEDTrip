<?php
namespace HealthCareAbroad\SearchBundle\Services;
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
        $this->searchStrategy->setResultType(SearchStrategy::RESULT_TYPE_ARRAY);

        return $this
            ->transformResults($this->searchStrategy->search($searchParams));
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
        $this->searchStrategy->setResultType(SearchStrategy::RESULT_TYPE_ARRAY);

        return $this
            ->transformResults($this->searchStrategy->search($searchParams));
    }

    public function loadSuggestions($parameters)
    {
        /*
        'searchParameter' =>
            array (size=1)
                'specialization' => string '3' (length=1)
        'filter' => string 'country' (length=7)
        'term' => string 'e' (length=1)
        */
//         /var_dump($parameters); exit;

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

            default:
        }

        return $results;
    }

    public function getTermDocuments(SearchParameterBag $searchParams)
    {
        $filters = array();

        //         if ($searchParams->get('specializationId', 0)) {
        //             $filters['specialization_id'] = $searchParams->get('specializationId');
        //         }

        //         if ($searchParams->get('subSpecializationId', 0)) {
        //             $filters['sub_specialization_id'] = $searchParams->get('subSpecializationId');
        //         }

        //         if ($searchParams->get('treatmentId', 0)) {
        //             $filters['treatment_id'] = $searchParams->get('treatmentId');
        //         }

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

    public function getMedicalCentersByTerm($term, $type = null)
    {
        return $this->searchStrategy->getMedicalCentersByTerm($term, $type);
    }

    public function getTerm($value, $options = array())
    {
        return $this->searchStrategy->getTerm($value, $options);
    }

    public function getRelatedTreatments($termId, $urlPrefix = '')
    {
        $categorized = array();

        //TODO: merge/optimize loops
        $sId = 0;
        $loopCounter = 1;
        $results = $this->searchStrategy->getRelatedTreatments($termId);
        $countItems = count($results);
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

        return $categorized;
    }

    //         specialization 1
    //             TREATMENTS
    //                 treatment 1
    //                 treatment 2
    //             SUBSPECIALIZATIONS
    //                 subspecialization 1
    //                     TREATMENTS
    //                         treatment 3
    //                 subspecialization 2
    //                     TREATMENTS
    //                         treatment 4
    //                         treatment 5
    //         specialization 2
    //             SUBSPECIALIZATION
    //                 subspecialization 3
    //                     TREATMENTS
    //                         treatment 6
    //         specialization 3
    //             TREATMENTS
    //                 treatment 7
    //                 treatment 8

}
