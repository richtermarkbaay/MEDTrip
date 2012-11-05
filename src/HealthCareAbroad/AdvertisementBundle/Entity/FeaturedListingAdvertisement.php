<?php
namespace HealthCareAbroad\AdvertisementBundle\Entity;

use HealthCareAbroad\AdvertisementBundle\Entity\Advertisement;

class FeaturedListingAdvertisement extends Advertisement
{
    /**
     * @var HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter
     */
    private $object;


    /**
     * Set object
     *
     * @param HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter $object
     * @return FeaturedListingAdvertisement
     */
    public function setObject(\HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter $object = null)
    {
        $this->object = $object;
        return $this;
    }

    /**
     * Get object
     *
     * @return HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter 
     */
    public function getObject()
    {
        return $this->object;
    }
}