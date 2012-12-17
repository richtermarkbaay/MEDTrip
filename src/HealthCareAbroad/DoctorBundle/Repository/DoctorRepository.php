<?php
namespace HealthCareAbroad\DoctorBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use HealthCareAbroad\DoctorBundle\Entity\Doctor;

class DoctorRepository extends EntityRepository
{
    public function getDoctorsBySearchTerm($searchTerm, $institutionId)
    {
        $query = $this->getEntityManager()->createQueryBuilder()
        ->select('a')
        ->from('DoctorBundle:Doctor', 'a')
        //->leftJoin('a.institutionDoctors', 'b', Join::WITH, 'b.institution = :institution')
        ->where('a.status = :active')
        //->andWhere('b.id IS NULL')
        ->andWhere('a.firstName LIKE :searchTerm OR a.middleName LIKE :searchTerm OR a.lastName LIKE :searchTerm')
        ->setParameter('searchTerm', '%'.$searchTerm.'%')
        //->setParameter('institution', $institutionId)
        ->setParameter('active', Doctor::STATUS_ACTIVE)
        ->getQuery();
    
        return $query->getResult();
    }
}