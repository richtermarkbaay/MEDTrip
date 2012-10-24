<?php
namespace HealthCareAbroad\AdvertisementBundle\Entity;

use HealthCareAbroad\AdvertisementBundle\Entity\Advertisement;

class FeaturedListingAdvertisement extends Advertisement
{
    
    /**
     * @var HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenterGroup
     */
    private $object;


    /**
     * Set object
     *
     * @param HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenterGroup $object
     * @return FeaturedListingAdvertisement
     */
    public function setObject(\HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenterGroup $object = null)
    {
        $this->object = $object;
        return $this;
    }

    /**
     * Get object
     *
     * @return HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenterGroup 
     */
    public function getObject()
    {
        return $this->object;
    }
}