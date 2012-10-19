<?php

namespace HealthCareAbroad\InstitutionBundle\Services;

use HealthCareAbroad\MedicalProcedureBundle\Entity\MedicalCenter;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenterGroupStatus;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenterGroup;

use Doctrine\Bundle\DoctrineBundle\Registry;

class InstitutionMedicalCenterGroupService
{
    /**
     * @var Registry
     */
    private $doctrine;
    
    public function setDoctrine(Registry $doctrine)
    {
        $this->doctrine = $doctrine;
    }
    
    /**
     * Save InstitutionMedicalCenterGroup to database
     * 
     * @param InstitutionMedicalCenterGroup $institutionMedicalCenterGroup
     * @return InstitutionMedicalCenterGroup
     */
    public function save(InstitutionMedicalCenterGroup $institutionMedicalCenterGroup)
    {
        $em = $this->doctrine->getEntityManager();
        $em->persist($institutionMedicalCenterGroup);
        $em->flush();
        
        return $institutionMedicalCenterGroup;
    }
    
    /**
     * Save an InstitutionMedicalCenterGroup as DRAFT
     * 
     * @param InstitutionMedicalCenterGroup $institutionMedicalCenterGroup
     */
    public function saveAsDraft(InstitutionMedicalCenterGroup $institutionMedicalCenterGroup)
    {
        $institutionMedicalCenterGroup->setStatus(InstitutionMedicalCenterGroupStatus::DRAFT);
        
        return $this->save($institutionMedicalCenterGroup);
    }
    
    /**
     * Check if InstitutionMedicalCenterGroup is of DRAFT status
     * 
     * @param InstitutionMedicalCenterGroup $institutionMedicalCenterGroup
     * @return boolean
     */
    public function isDraft(InstitutionMedicalCenterGroup $institutionMedicalCenterGroup)
    {
        return $institutionMedicalCenterGroup->getStatus() == InstitutionMedicalCenterGroupStatus::DRAFT;
    }
}