<?php

namespace HealthCareAbroad\InstitutionBundle\Repository;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenterStatus;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter;

use HealthCareAbroad\HelperBundle\Classes\QueryOptionBag;

use HealthCareAbroad\InstitutionBundle\Entity\Institution;

use Doctrine\ORM\EntityRepository;

/**
 * InstitutionRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class InstitutionRepository extends EntityRepository
{
    public function search($term = '', $limit = 10)
    {
        $dql = "
            SELECT i
            FROM InstitutionBundle:Institution AS i
            WHERE i.name LIKE :term
            ORDER BY i.name ASC"
        ;
    
        $query = $this->_em->createQuery($dql);
        $query->setParameter('term', "%$term%");
        $query->setMaxResults($limit);
    
        return $query->getResult();
    }

    /**
     * Get active institution medical centers
     * 
     * @param Institution $institution
     * @param QueryOptionBag $queryOptions
     * @return InstitutionMedicalCenter
     */
    public function getActiveInstitutionMedicalCenters(Institution $institution, QueryOptionBag $queryOptions=null)
    {
        $dql = "SELECT a FROM InstitutionBundle:InstitutionMedicalCenter a WHERE a.institution = :institutionId AND a.status = :active ";
        $query = $this->_em->createQuery($dql)
            ->setParameter('institutionId', $institution->getId())
            ->setParameter('active', InstitutionMedicalCenterStatus::APPROVED);
        
        return $query->getResult();
    }
    
    /**
     * Get draft institution medical centers
     *
     * @param Institution $institution
     * @param QueryOptionBag $queryOptions
     * @return InstitutionMedicalCenter
     */

    public function getDraftInstitutionMedicalCenters(Institution $institution, QueryOptionBag $queryOptions=null)
    {
        $dql = "SELECT a FROM InstitutionBundle:InstitutionMedicalCenter a WHERE a.institution = :institutionId AND a.status = :active ";
        $query = $this->_em->createQuery($dql)
        ->setParameter('institutionId', $institution->getId())
        ->setParameter('active', InstitutionMedicalCenterStatus::DRAFT);
    
        return $query->getResult();
    }    
}