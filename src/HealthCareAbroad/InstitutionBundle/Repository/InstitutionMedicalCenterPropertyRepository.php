<?php

namespace HealthCareAbroad\InstitutionBundle\Repository;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionProperty;

use Doctrine\ORM\EntityRepository;

/**
 * InstitutionPropertyRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class InstitutionMedicalCenterPropertyRepository extends EntityRepository
{
    /**
     * Get all ancilliary services
     */
    public function getAllServicesByInstitutionMedicalCenter($imcId, $institutionId)
    {
        $connection = $this->getEntityManager()->getConnection();
        $query = "SELECT * FROM institution_medical_center_properties a JOIN offered_services b ON b.id = a.value WHERE a.institution_id = :id and a.institution_medical_center_id = :imcId";
        $stmt = $connection->prepare($query);
        $stmt->bindValue('id', $institutionId);
        $stmt->bindValue('imcId', $imcId);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}