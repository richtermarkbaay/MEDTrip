<?php
/**
 *
 * @author Allejo Chris G. Velarde
 *
 */

namespace HealthCareAbroad\AdvertisementBundle\Repository;

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
}
