<?php

namespace HealthCareAbroad\InstitutionBundle\Repository;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter;

use Doctrine\ORM\EntityRepository;

class BusinessHourRepository extends EntityRepository
{
    public function deleteByInstitutionMedicalCenter(InstitutionMedicalCenter $institutionMedicalCenter)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        
        $qb->delete('InstitutionBundle:BusinessHour bh')
            ->where('bh.institutionMedicalCenter = :institutionMedicalCenter')
            ->setParameter('institutionMedicalCenter', $institutionMedicalCenter);
        
        $qb->getQuery()->execute();
        
    }
}