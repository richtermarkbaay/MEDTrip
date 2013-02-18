<?php
/**
 *
 * @author Adelbert D. Silla
 *
 */

namespace HealthCareAbroad\AdvertisementBundle\Repository;

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
        ->andWhere($qb->expr()->in('a.advertisementType', ':advertisementTypes'))
        ->setParameter('advertisementTypes', $types)
        ->setParameter('status', 1);
        
        return $qb->getQuery()->getResult();
    }
    
    
    public function getActiveFeaturedClinic()
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
           ->where('a.advertisementType = :type')
           ->andWhere('a.institutionMedicalCenterId IS NOT NULL')
           ->andWhere('a.status = :status')
           ->setParameter('type', 2)
           ->setParameter('status', 1);

        $result = $qb->getQuery()->getResult();

        return $result;
    }

    public function getFeaturedDestinations()
    {
        $qb = $this->createQueryBuilder('a');
        $qb->select('a, b, c,d,gal')
        ->leftJoin('a.institution', 'b')
        ->leftJoin('b.gallery', 'gal')
        ->leftJoin('a.media', 'c')
        ->leftJoin('a.treatment', 'd')
        ->where('a.advertisementType = :type')
        ->andWhere('a.status = :status')
        ->setParameter('type', 3)
        ->setParameter('status', 1);
    
        $result = $qb->getQuery()->getResult();
    
        return $result;
    }

    public function getActiveNews()
    {
        $qb = $this->createQueryBuilder('a');
        $qb->select('a,b, c')
           ->leftJoin('a.institution', 'b')
           ->leftJoin('a.media', 'c')
           ->where('a.advertisementType = :type')
           ->andWhere('a.status = :status')
           ->setParameter('type', 6)
           ->setParameter('status', 1);

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
        ->setParameter('type', 7)
        ->setParameter('status', 1);

        $result = $qb->getQuery()->getResult();

        return $result;
    }
}
