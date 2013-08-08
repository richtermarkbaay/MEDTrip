<?php

namespace HealthCareAbroad\InstitutionBundle\Repository;

use Doctrine\ORM\QueryBuilder;

use Doctrine\ORM\Query;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionPropertyType;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenterStatus;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionStatus;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenterGroupStatus;

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
    /**
     * 
     * @param string $slug
     * @return int
     */
    public function getInstitutionIdBySlug($slug)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('inst.id')
        ->from('InstitutionBundle:Institution', 'inst')
        ->where('inst.slug = :slug')
        ->setParameter('slug', $slug);
        
        return (int)$qb->getQuery()->getOneOrNullResult(Query::HYDRATE_SINGLE_SCALAR);
    }
    
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
        $dql = "SELECT a FROM InstitutionBundle:InstitutionMedicalCenter a WHERE a.institution = :institutionId AND a.status != :inActive ";
        $query = $this->_em->createQuery($dql)
            ->setParameter('institutionId', $institution->getId())
            ->setParameter('inActive', InstitutionMedicalCenterStatus::INACTIVE);

        return $query->getResult();
    }
    
    public function countActiveInstitutionMedicalCenters(Institution $institution)
    {
        $query = $this->_em->createQueryBuilder()
            ->select('count(a.id)')
            ->from('InstitutionBundle:InstitutionMedicalCenter', 'a')
            ->where('a.institution = :institutionId')
            ->andWhere('a.status != :inActive')
            ->setParameter('institutionId', $institution->getId())
            ->setParameter('inActive', InstitutionMedicalCenterStatus::INACTIVE)
            ->getQuery();
        
        return $query->getSingleScalarResult();
    }

    /**
     * Get draft institution specializations
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
        ->setParameter('active', InstitutionMedicalCenterGroupStatus::DRAFT);

        return $query->getResult();
    }

    /**
     * @return QueryBuilder
     */
    public function getQueryBuilderForApprovedInstitutions()
    {
        $qb = $this->createQueryBuilder('a')
            ->where('a.status = :approved_status')
            ->setParameter('approved_status', InstitutionStatus::getBitValueForApprovedStatus())
            ->orderBy('a.name', 'ASC');

        return $qb;
    }

    public function getInstitutionsByCountry($country)
    {
        // FIXME: This query does not consider institutions that have no institution specializations, if this will be used in search strategy
        $dql = "SELECT a
                FROM InstitutionBundle:Institution a
                INNER JOIN a.institutionMedicalCenters b
                WHERE a.country = :country
                AND a.status = :status
                AND b.status = :imcStatus ";

        $query = $this->_em->createQuery($dql)
            ->setParameter('status', InstitutionStatus::getBitValueForActiveAndApprovedStatus())
            ->setParameter('country', $country)
            ->setParameter('imcStatus', InstitutionMedicalCenterStatus::APPROVED);

        return $query->getResult();
    }

    public function getInstitutionsByCity($city)
    {
        $dql = "SELECT a
                FROM InstitutionBundle:Institution a
                INNER JOIN a.institutionMedicalCenters b
                WHERE a.city = :city
                AND a.city IS NOT NULL
                AND a.status = :status
                AND b.status = :imcStatus";

        $query = $this->_em->createQuery($dql)
            ->setParameter('status', InstitutionStatus::getBitValueForActiveAndApprovedStatus())
            ->setParameter('imcStatus', InstitutionMedicalCenterStatus::APPROVED)
            ->setParameter('city', $city);

        return $query->getResult();
    }

    /**
     * Get global awards of an institution
     *
     * @param Institution $institution
     * @return array GlobalAward
     */
    public function getAllGlobalAwardsByInstitution($institution, $hydrationMode=Query::HYDRATE_OBJECT)
    {
        $institutionId = $institution;
        if ($institution instanceof Institution ){
            $institutionId = $institution->getId();
        }
        
        //$globalAwardPropertyType = $this->getEntityManager()->getRepository('InstitutionBundle:InstitutionPropertyType')->findOneBy(array('name' => InstitutionPropertyType::TYPE_GLOBAL_AWARD));

        $sql = "SELECT a.value  FROM institution_properties a ".
                        "WHERE a.institution_property_type_id = :propertyType AND a.institution_id = :institutionId";
        $statement = $this->getEntityManager()
        ->getConnection()->prepare($sql);

        $statement->execute(array('propertyType' => InstitutionPropertyType::GLOBAL_AWARD_ID, 'institutionId' => $institutionId));

        $result = array();
        if($statement->rowCount() > 0) {
            $ids = array();
            while ($row = $statement->fetch(Query::HYDRATE_ARRAY)) {
                $ids[] = $row['value'];
            }

            $dql = "SELECT a, b FROM HelperBundle:GlobalAward a INNER JOIN a.awardingBody as b WHERE a.id IN (?1)";
            $query = $this->getEntityManager()->createQuery($dql)
            ->setParameter(1, $ids);

            $result = $query->getResult($hydrationMode);
        }

        return $result;

    }
    
    // TODO - MUST FIX MULTIPLE QUERY FOR GALLERY BUG AND REMOVE gallery LEFT JOIN.
    public function getAllInstitutions()
    {
         $qb = $this->createQueryBuilder('a');
         $query = $qb->select('a, b, c')
            ->leftJoin('a.institutionUsers', 'b')
            ->leftJoin('a.gallery', 'c') // This isn't required
            ->orderBy('a.name', 'ASC')
            ->getQuery();
         
        return $query->getResult();
    }
    
    public function getAllInstitutionByParams($params)
    {
        $query = $this->createQueryBuilder('a')
        ->where('a.status = :status')
        ->andWhere('a.name LIKE :searchTerm')
        ->setParameter('status', InstitutionStatus::getBitValueForActiveAndApprovedStatus())
        ->setParameter('searchTerm', '%'.$params['searchTerm'].'%');
    
        if($params['countryId'] != 'all') {
            $query->andWhere('a.country = :country')->setParameter('country', $params['countryId']);
        }
    
        if($params['cityId'] != 'all') {
            $query->andWhere('a.city = :city')->setParameter('city', $params['cityId']);
        }
        $query = $query->orderBy('a.name');
    
        return $query->getQuery()->getResult();
    }
}