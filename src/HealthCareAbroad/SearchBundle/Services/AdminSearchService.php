<?php
namespace HealthCareAbroad\SearchBundle\Services;

class AdminSearchService
{
    const SEARCH_CATEGORY_INSTITUTION =       1;
    const SEARCH_CATEGORY_CENTER =            2;
    const SEARCH_CATEGORY_SPECIALIZATION =    3;
    const SEARCH_CATEGORY_SUBSPECIALIZATION = 4;
    const SEARCH_CATEGORY_TREATMENT =         5;

    private $entityManager;

    private $repositoryMap = array(
        self::SEARCH_CATEGORY_INSTITUTION => 'InstitutionBundle:Institution',
        self::SEARCH_CATEGORY_CENTER => 'InstitutionBundle:InstitutionMedicalCenter',
        self::SEARCH_CATEGORY_SPECIALIZATION => 'TreatmentBundle:Specialization',
        self::SEARCH_CATEGORY_SUBSPECIALIZATION => 'TreatmentBundle:SubSpecialization',
        self::SEARCH_CATEGORY_TREATMENT => 'TreatmentBundle:Treatment'
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
     * This simply delegates the actual searching to the corresponding
     * repository class of the search entity.
     *
     * @param array $searchCriteria
     */
    public function execute(array $searchCriteria = array())
    {
        return $this->entityManager->getRepository($this->repositoryMap[$searchCriteria['category']])->search($searchCriteria['term']);
    }
}