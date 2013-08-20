<?php
/**
 *
 * @author Adelbert D. Silla
 *
 */

namespace HealthCareAbroad\AdvertisementBundle\Repository;

use HealthCareAbroad\AdvertisementBundle\Entity\AdvertisementStatuses;

use Doctrine\ORM\EntityRepository;

class AdvertisementDenormalizedPropertyRepository extends EntityRepository
{
    public function getActiveAdvertisementsByType(array $types)
    {
        $qb = $this->createQueryBuilder('a');
        $qb->select('a,b,c,gal,co,ci')
        ->leftJoin('a.institution', 'b')
        ->leftJoin('b.country', 'co')
        ->leftJoin('b.city', 'ci')
        ->leftJoin('b.gallery', 'gal')
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

    public function getActiveFeaturedClinicByCriteria(array $criteria = array(), $limit = null)
    {
        $qb = $this->createQueryBuilder('a');
        $qb->select('a,b,c,d,gal,co,ci, imcLogo')
           ->leftJoin('a.institution', 'b')
           ->leftJoin('b.country', 'co')
           ->leftJoin('b.city', 'ci')
           ->leftJoin('b.gallery', 'gal')
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

        $result = $qb->getQuery()->getResult();

        return $result;
    }

    public function getActiveFeaturedInstitutionByCriteria(array $criteria = array(), $limit = null)
    {

        $qb = $this->createQueryBuilder('a');
        $qb->select('a,b,d,gal,co,ci')
        ->leftJoin('a.institution', 'b')
        ->leftJoin('b.country', 'co')
        ->leftJoin('b.city', 'ci')
        ->leftJoin('b.gallery', 'gal')
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
        $qb->select('a, b, c,d,gal')
        ->leftJoin('a.institution', 'b')
        ->leftJoin('b.gallery', 'gal')
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
