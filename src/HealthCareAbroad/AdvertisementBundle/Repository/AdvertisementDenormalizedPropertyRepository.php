<?php
/**
 *
 * @author Adelbert D. Silla
 *
 */

namespace HealthCareAbroad\AdvertisementBundle\Repository;

use Doctrine\ORM\Query;

use HealthCareAbroad\AdvertisementBundle\Entity\AdvertisementStatuses;

use Doctrine\ORM\EntityRepository;

class AdvertisementDenormalizedPropertyRepository extends EntityRepository
{
    public function getActiveAdvertisementsByType(array $types)
    {
        $qb = $this->createQueryBuilder('a');
        $qb->select('a,b,c,co,ci')
        ->leftJoin('a.institution', 'b')
        ->leftJoin('b.country', 'co')
        ->leftJoin('b.city', 'ci')
        ->leftJoin('a.media', 'c')
        ->where('a.status = :status')
        ->andWhere('a.dateExpiry > :dateExpiry')
        ->andWhere($qb->expr()->in('a.advertisementType', ':advertisementTypes'))
        ->orderBy('a.dateExpiry', 'ASC')
        ->setParameter('advertisementTypes', $types)
        ->setParameter('status', AdvertisementStatuses::ACTIVE)
        ->setParameter('dateExpiry', new \DateTime());
        
        return $qb->getQuery()->getResult();
    }

    public function getActiveFeaturedClinicByCriteria(array $criteria = array(), $limit = null, $hydrationMode=Query::HYDRATE_OBJECT)
    {
        $qb = $this->createQueryBuilder('a');
        $qb->select('a,b,c,d,co,ci, imcLogo')
           ->leftJoin('a.institution', 'b')
           ->leftJoin('b.country', 'co')
           ->leftJoin('b.city', 'ci')
           ->innerJoin('a.institutionMedicalCenter', 'c')
           ->leftJoin('c.logo', 'imcLogo')
           ->leftJoin('a.media', 'd')
           ->andWhere('a.institutionMedicalCenterId IS NOT NULL')
           ->andWhere('a.status = :status')
           ->andWhere('a.dateExpiry > :dateExpiry')
           ->orderBy('a.dateExpiry', 'ASC')
           ->setParameter('status', AdvertisementStatuses::ACTIVE)
           ->setParameter('dateExpiry', new \DateTime());

        foreach($criteria as $key => $value) {
            $qb->andWhere("a.$key = :$key")->setParameter($key, $value);
        }

        if(!is_null($limit)) {
            $qb->setMaxResults($limit);
        }

        $result = $qb->getQuery()->getResult($hydrationMode);

        return $result;
    }

    public function getActiveFeaturedInstitutionByCriteria(array $criteria = array(), $limit = null)
    {

        $qb = $this->createQueryBuilder('a');
        $qb->select('a,b,d,co,ci')
        ->leftJoin('a.institution', 'b')
        ->leftJoin('b.country', 'co')
        ->leftJoin('b.city', 'ci')
        ->leftJoin('a.media', 'd')
        ->andWhere('a.status = :status')
        ->andWhere('a.dateExpiry > :dateExpiry')
        ->orderBy('a.dateExpiry', 'ASC')
        ->setParameter('status', AdvertisementStatuses::ACTIVE)
        ->setParameter('dateExpiry', new \DateTime());

        foreach($criteria as $key => $value) {
            $qb->andWhere("a.$key = :$key")->setParameter($key, $value);
        }

        if(!is_null($limit)) {
            $qb->setMaxResults($limit);
        }

        $result = $qb->getQuery()->getResult();

        return $result;
    }

    public function getActiveNews()
    {
        $qb = $this->createQueryBuilder('a');
        $qb->select('a,b,c')
           ->leftJoin('a.institution', 'b')
           ->leftJoin('a.media', 'c')
           ->where('a.advertisementType = :type')
           ->andWhere('a.status = :status')
           ->andWhere('a.dateExpiry > :dateExpiry')
           ->orderBy('a.dateExpiry', 'ASC')
           ->setParameter('type', 6)
           ->setParameter('status', AdvertisementStatuses::ACTIVE)
           ->setParameter('dateExpiry', new \DateTime());

        $result = $qb->getQuery()->getResult();

        return $result;
    }

    public function getCommonTreatments()
    {
        $qb = $this->createQueryBuilder('a');
        $qb->select('a, b, c,d')
        ->leftJoin('a.institution', 'b')
        ->leftJoin('a.media', 'c')
        ->leftJoin('a.treatment', 'd')
        ->where('a.advertisementType = :type')
        ->andWhere('a.status = :status')
        ->andWhere('a.dateExpiry > :dateExpiry')
        ->orderBy('a.dateExpiry', 'ASC')
        ->setParameter('type', 7)
        ->setParameter('status', AdvertisementStatuses::ACTIVE)
        ->setParameter('dateExpiry', new \DateTime());

        $result = $qb->getQuery()->getResult();

        return $result;
    }
    
    public function getActiveSearchResultsImageAds(array $criteria = array(), $limit = null)
    {
        $qb = $this->createQueryBuilder('a');
        $qb->select('a,b,c')
        ->leftJoin('a.institution', 'b')
        ->leftJoin('a.media', 'c')
        ->where('a.advertisementType = :type')
        ->andWhere('a.status = :status')
        ->andWhere('a.dateExpiry > :dateExpiry')
        ->orderBy('a.dateExpiry', 'ASC')
        ->setParameter('type', 13)
        ->setParameter('status', AdvertisementStatuses::ACTIVE)
        ->setParameter('dateExpiry', new \DateTime());

        foreach($criteria as $key => $value) {
            $qb->andWhere("a.$key = :$key")->setParameter($key, $value);
        }
        
        if(!is_null($limit)) {
            $qb->setMaxResults($limit);
        }
        
        $result = $qb->getQuery()->getResult();
    
        return $result;
    }
}
