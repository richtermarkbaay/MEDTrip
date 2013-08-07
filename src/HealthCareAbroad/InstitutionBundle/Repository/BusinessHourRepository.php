<?php

namespace HealthCareAbroad\InstitutionBundle\Repository;

use Doctrine\ORM\Query;

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
    
    /**
     * 
     * @param Mixed <InstitutionMedicalCenter, int> $institutionMedicalCenter
     */
    public function getByInstitutionMedicalCenter($institutionMedicalCenter, $hydrationMode=Query::HYDRATE_OBJECT)
    {
        if ($institutionMedicalCenter instanceof InstitutionMedicalCenter) {
            $institutionMedicalCenterId = $institutionMedicalCenter->getId();
        }
        else {
            $institutionMedicalCenterId = $institutionMedicalCenter;
        }
        
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('bh')
            ->from('InstitutionBundle:BusinessHour', 'bh')
            ->where('bh.institutionMedicalCenter = :institutionMedicalCenterId')
            ->setParameter('institutionMedicalCenterId', $institutionMedicalCenterId);
        
        return $qb->getQuery()->getResult($hydrationMode);
    }
}