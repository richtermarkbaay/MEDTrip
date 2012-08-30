<?php
namespace HealthCareAbroad\InstitutionBundle\Services;

use HealthCareAbroad\InstitutionBundle\Repository\InstitutionMedicalCenterRepository;

use Doctrine\Bundle\DoctrineBundle\Registry;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter;

/**
 * Service class for InstitutionMedicalCenter
 * @author Allejo Chris G. Velarde
 *
 */
class InstitutionMedicalCenterService
{
    /**
     * @var Registry
     */
    private $doctrine;
    
    /**
     * @var InstitutionMedicalCenterRepository
     */
    private $repository;
    
    public function __construct(Registry $doctrine)
    {
        $this->doctrine = $doctrine;
        $this->repository = $this->doctrine->getRepository('InstitutionBundle:InstitutionMedicalCenter');
    }
    
    /**
     * Get InstitutionMedicalProcedureTypes of an InstitutionMedicalCenter
     * 
     * @param InstitutionMedicalCenter $institutionMedicalCenter
     */
    public function getInstitutionMedicalProcedureTypesOfCenter(InstitutionMedicalCenter $institutionMedicalCenter)
    {
        return $this->doctrine->getRepository('InstitutionBundle:InstitutionMedicalProcedureType')->getByInstitutionMedicalCenter($institutionMedicalCenter);
    }
}