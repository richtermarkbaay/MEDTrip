<?php

namespace HealthCareAbroad\FrontendBundle\Services;

use Doctrine\DBAL\Query\QueryBuilder;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionStatus;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenterStatus;

use Doctrine\Bundle\DoctrineBundle\Registry;

class FrontendInstitutionService
{
    /**
     * @var Registry
     */
    private $doctrine;
    
    public function setDoctrine(Registry $v)
    {
        $this->doctrine = $v;
    }
    
    public function getFullInstitutionBySlug($slug)
    {
        $qb = $this->getEagerlyLoadedQueryBuilder();
        $qb->andWhere($where)            
        ->setParameter('institutionSlug', $slug)
        ->setParameter('status', InstitutionStatus::getBitValueForApprovedStatus())
        ->setParameter('medicalCenterStatus', InstitutionMedicalCenterStatus::APPROVED);
    
        $institution =  $qb->getQuery()->getOneOrNullResult();
    
        return $institution;
    }
    
    /**
     * @return QueryBuilder
     */
    private function getEagerlyLoadedQueryBuilder()
    {
        $qb = $this->doctrine->getEntityManager()->createQueryBuilder();
        $qb->select('inst, imc, co, ct, st, glr, lgo')
            ->from('InstitutionBundle:Institution', 'inst')
            ->leftJoin('a.institutionMedicalCenters ', 'imc', Join::WITH, 'imc.status = :medicalCenterStatus')
                ->setParameter('medicalCenterStatus', InstitutionMedicalCenterStatus::APPROVED)
            ->leftJoin('inst.country', 'co')
            ->leftJoin('inst.city', 'ct')
            ->leftJoin('inst.state', 'st')
            ->leftJoin('inst.gallery', 'glr')
            ->leftJoin('inst.logo', 'lgo')
            ->where('1=1')
            ->andWhere('inst.status = :approvedStatus')
                ->setParameter('approvedStatus', InstitutionStatus::getBitValueForApprovedStatus());
        
        return $qb;
    }
}