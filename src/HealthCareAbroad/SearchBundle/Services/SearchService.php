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

    private function transformResults(array $results)
    {
        if ($this->searchStrategy->isViewReadyResults()) {
            return $results;
        }

        $label = $value = '';
        $transformedResults = array();

        //TODO: optimize this loop
        foreach ($results as $result) {
            switch (get_class($result)) {
                case 'HealthCareAbroad\HelperBundle\Entity\Country':
                    $label = $result->getName();
                    $value = $result->getId().'-0';
                    break;

                case 'HealthCareAbroad\HelperBundle\Entity\City':
                    $label = $result->getName().', '.$result->getCountry()->getName();
                    $value = $result->getCountry()->getId().'-'.$result->getId();
                    break;

                case 'HealthCareAbroad\TreatmentBundle\Entity\Specialization':
                    $label = $result->getName();
                    $value = $result->getId().'-0-0-specialization';
                    break;

                case 'HealthCareAbroad\TreatmentBundle\Entity\SubSpecialization':
                    $label = $result->getName();
                    $value = $result->getSpecialization()->getId().'-'.$result->getId().'-0-subSpecialization';
                    break;

                case 'HealthCareAbroad\TreatmentBundle\Entity\Treatment':
                    $label = $result->getName();
                    $value = $result->getSpecialization()->getId().'-0-'.$result->getId().'-treatment';
                    break;

                default:
            }

            $transformedResults[] = array('label' => $label, 'value' => $value);
        }

        return $transformedResults;
    }

    public function searchByCountry($country)
    {
        return $this->searchStrategy->searchForInstitutionsByCountry($country);
    }

    public function searchByCity($city)
    {
        return $this->searchStrategy->searchForInstitutionsByCity($city);
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
}