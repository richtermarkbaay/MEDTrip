<?php

namespace HealthCareAbroad\InstitutionBundle\Repository;

use HealthCareAbroad\InstitutionBundle\Entity\Doctor;

use Doctrine\ORM\EntityRepository;

/**
 * DoctorRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class DoctorRepository extends EntityRepository
{
    /**
     * Get all doctors that are active
     */
    public function getActiveDoctors()
    {
        $dql = "SELECT a FROM InstitutionBundle:Doctor a WHERE a.status = :active";
    
        $query = $this->getEntityManager()->createQuery($dql)
        ->setParameter('active', Doctor::STATUS_ACTIVE);
        return $query->getResult();
    }
}