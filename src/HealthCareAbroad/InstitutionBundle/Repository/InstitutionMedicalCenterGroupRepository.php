<?php
namespace HealthCareAbroad\InstitutionBundle\Repository;

use HealthCareAbroad\MedicalProcedureBundle\Entity\MedicalCenter;

use Doctrine\ORM\QueryBuilder;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenterGroup;

use Doctrine\ORM\EntityRepository;

/**
 * 
 * @author Allejo Chris G. Velarde
 *
 */
class InstitutionMedicalCenterGroupRepository extends EntityRepository
{
    public function getQueryBuilderForAvailableTreatmentsBySpecialization(InstitutionMedicalCenterGroup $institutionMedicalCenterGroup, MedicalCenter $medicalCenter)
    {
        /**
         SELECT a.* FROM `treatments` a 
         LEFT JOIN `institution_treatments` b
         ON a.id = b.treatment_id AND b.institution_medical_center_group_id = 1
         WHERE a.medical_center_id = 8
         **/
        $qb = $this->getEntityManager()->createQueryBuilder();
    }
}