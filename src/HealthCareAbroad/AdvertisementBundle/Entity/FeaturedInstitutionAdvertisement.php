<?php
namespace HealthCareAbroad\AdvertisementBundle\Entity;

use HealthCareAbroad\AdvertisementBundle\Entity\Advertisement;

class FeaturedInstitutionAdvertisement extends Advertisement
{
    /**
     * @var HealthCareAbroad\InstitutionBundle\Entity\Institution
     */
    private $object;


    /**
     * Set object
     *
     * @param HealthCareAbroad\InstitutionBundle\Entity\Institution $object
     * @return FeaturedInstitutionAdvertisement
     */
    public function setObject(\HealthCareAbroad\InstitutionBundle\Entity\Institution $object = null)
    {
        $this->object = $object;
        return $this;
    }

    /**
     * Get object
     *
     * @return HealthCareAbroad\InstitutionBundle\Entity\Institution 
     */
    public function getObject()
    {
        return $this->object;
    }
}