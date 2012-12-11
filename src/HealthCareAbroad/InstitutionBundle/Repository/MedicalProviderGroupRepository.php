<?php

namespace HealthCareAbroad\InstitutionBundle\Repository;

use HealthCareAbroad\InstitutionBundle\Entity\MedicalProviderGroup;

use Doctrine\ORM\EntityRepository;

class MedicalProviderGroupRepository extends EntityRepository
{
    public function getActiveMedicalGroups($limit = null)
    {
        $qb = $this->createQueryBuilder('b')
        ->select('b')
        ->add('from', 'InstitutionBundle:MedicalProviderGroup b')
        ->add('where', 'b.status = :status')
        ->setParameter('status', MedicalProviderGroup::STATUS_ACTIVE)
        ->addOrderBy('b.name', 'ASC');
    
        if (false === is_null($limit))
            $qb->setMaxResults($limit);
    
        return $qb->getQuery()
        ->getResult();
    }
    
}