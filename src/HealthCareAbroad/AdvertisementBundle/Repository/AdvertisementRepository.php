<?php
/**
 *
 * @author Allejo Chris G. Velarde
 *
 */

namespace HealthCareAbroad\AdvertisementBundle\Repository;

use HealthCareAbroad\AdvertisementBundle\Entity\AdvertisementTypes;

use HealthCareAbroad\AdvertisementBundle\Entity\AdvertisementStatuses;

use HealthCareAbroad\AdvertisementBundle\Entity\Advertisement;

use HealthCareAbroad\HelperBundle\Classes\QueryOption;

use Doctrine\ORM\EntityRepository;

class AdvertisementRepository extends EntityRepository
{
    /**
     * Get active Advertisement by type discriminator column. Do not apply caching here, instead apply it in service class using this function.
     *
     * @param int $advertisementType
     * @param QueryOption $option
     * @return array Advertisement
     */
    public function getActiveAdvertisementsByType($advertisementType, QueryOption $option=null)
    {
        $qb = $this->getQueryBuilderForAdvertisementsByType($advertisementType, $option);

        $results = $qb->getQuery()->getResult();

        return $results;
    }

    public function getQueryBuilderForAdvertisementsByType($advertisementType, QueryOption $option=null){


        $classMapping = AdvertisementTypes::getDiscriminatorMapping();
        if (!\array_key_exists($advertisementType, $classMapping)) {
            throw new \Exception("Invalid advertisement type '{$advertisementType}' passed to ".__CLASS__."::getActiveAdvertisementsByType.");
        }
        $advertisementTypeClass = $classMapping[$advertisementType];

        $qb = $this->getEntityManager()->createQueryBuilder()
        ->select('a')
        ->from($advertisementTypeClass, 'a')
        ;

        return $qb;
    }

}
