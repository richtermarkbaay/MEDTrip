<?php
/**
 *
 * @author Adelbert D. Silla
 *
 */

namespace HealthCareAbroad\AdvertisementBundle\Repository;

use Doctrine\ORM\Query\ResultSetMapping;

use Doctrine\ORM\Query;

use HealthCareAbroad\AdvertisementBundle\Entity\AdvertisementStatuses;

use HealthCareAbroad\HelperBundle\Classes\QueryOption;

use Doctrine\ORM\EntityRepository;

class AdvertisementDenormalizedPropertyRepository extends EntityRepository
{
    /**
     * Get active Advertisement by type discriminator column. Do not apply caching here, instead apply it in service class using this function.
     *
     * @return array Advertisement
     */
    public function getActiveByType($type)
    {
        $sql = "SELECT a.title as adTitle, a.description as adDescription FROM advertisement_denormalized_properties as a left join institutions as b on a.institution_id = b.id left join countries as c ON b.country_id = c.id left join cities as d on b.city_id = d.id left join media as e on b.media_id = e.id";

        switch($type) {
            case 1: // 	Premier Home Page Feature 
//                $sql .= "LEFT JOIN media AS d ON a.media_id = d.id ";
                break;
        }

//        $sql .= " WHERE a.advertisement_type_id = $type";

        $sql = "select u.id from advertisement_denormalized_properties u";
        
        $rsm = new ResultSetMapping();
        $rsm->addEntityResult('AdvertisementDenormalizedProperty', 'u');
        $rsm->addFieldResult('u', 'id', 'id');
        $query = $this->_em->createNativeQuery($sql, $rsm);
        $result = $query->getResult();
        //$result = $conn->executeQuery($sql)->fetchAll(Query::HYDRATE_ARRAY);
        var_dump($result);
        exit;
        return $results;
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
}
