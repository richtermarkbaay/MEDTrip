<?php
namespace HealthCareAbroad\SearchBundle\Services;

use Doctrine\ORM\QueryBuilder;

use HealthCareAbroad\MedicalProcedureBundle\Entity\Treatment;
use HealthCareAbroad\SearchBundle\Constants;
use HealthCareAbroad\HelperBundle\Entity\Country;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\EntityManager;

/**
 * Temporary holder of all search related functionality
 *
 */
class AdminSearchService
{
    private $entityManager;
    private $repositoryMap = array(
        Constants::SEARCH_CATEGORY_INSTITUTION => 'InstitutionBundle:Institution',
        Constants::SEARCH_CATEGORY_CENTER => 'MedicalProcedureBundle:MedicalCenter',
        Constants::SEARCH_CATEGORY_PROCEDURE_TYPE => 'MedicalProcedureBundle:Treatment',
        Constants::SEARCH_CATEGORY_PROCEDURE => 'MedicalProcedureBundle:TreatmentProcedure'
    );

    /**
     * Constructor
     *
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     *
     * @param array $searchCriteria
     * @todo rename method
     */
    public function initiate(array $searchCriteria = array())
    {
        $repository = $this->entityManager->getRepository($this->repositoryMap[$searchCriteria['category']]);

        return $repository->search($searchCriteria['term']);
    }
}