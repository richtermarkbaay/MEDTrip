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
    /**
     * Get active Advertisement by type discriminator column. Do not apply caching here, instead apply it in service class using this function.
     *
     * @return array Advertisement
     */
    public function getActiveHomepagePremier()
    {
        $qb = $this->createQueryBuilder('a');
        $qb->select('a,b,c')
           ->leftJoin('a.institution', 'b')
           ->leftJoin('a.media', 'c')
           ->where('a.advertisementType = :type')
           ->andWhere('a.status = :status')
           ->setParameter('type', 1)
           ->setParameter('status', 1);

        $result = $qb->getQuery()->getResult();

        return $result;
    }

    public function getActiveFeaturedClinic()
    {
        $qb = $this->createQueryBuilder('a');
        $qb->select('a,b,c,d')
           ->leftJoin('a.institution', 'b')
           ->leftJoin('a.institutionMedicalCenter', 'c')
           ->leftJoin('a.media', 'd')
           ->where('a.advertisementType = :type')
           ->andWhere('a.institutionMedicalCenterId IS NOT NULL')
           ->andWhere('a.status = :status')
           ->setParameter('type', 2)
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
        $qb->select('a, b, c', 'd')
        ->leftJoin('a.institution', 'b')
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
