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
     * Short description
     *
     * @param SearchParameterBag $searchParams Strategy class to use
     *
     * @return Ambigous <multitype:, multitype:multitype:string  >
     */
    public function getDestinations(SearchParameterBag $searchParams)
    {
        $this->searchStrategy->setResultType(SearchStrategy::RESULT_TYPE_ARRAY);

        return $this->transformResults($this->searchStrategy->search($searchParams));
    }

    /**
     *  Short description
     *
     * @param SearchParameterBag $searchParams Strategy class to use
     *
     * @return Ambigous <multitype:, multitype:multitype:string  >
     */
    public function getTreatments(SearchParameterBag $searchParams)
    {
        $this->searchStrategy->setResultType(SearchStrategy::RESULT_TYPE_ARRAY);

        return $this->transformResults($this->searchStrategy->search($searchParams));
    }

    public function getTermDocuments(SearchParameterBag $searchParams)
    {
        return $this->searchStrategy->getTermDocuments($searchParams);
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
        return $this->searchStrategy->searchMedicalCentersBySpecialization($specialization);
    }

    public function searchBySubSpecialization($subSpecialization)
    {
        return $this->searchStrategy->searchMedicalCentersBySubSpecialization($subSpecialization);
    }

    public function searchByTreatment($treatment)
    {
        return $this->searchStrategy->searchMedicalCentersByTreatment($treatment);
    }

    public function getMedicalCentersByTerm($term, $type = null)
    {
        return $this->searchStrategy->getMedicalCentersByTerm($term, $type);
    }
}