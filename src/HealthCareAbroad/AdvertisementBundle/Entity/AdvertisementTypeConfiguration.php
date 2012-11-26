<?php

namespace HealthCareAbroad\AdvertisementBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HealthCareAbroad\AdvertisementBundle\Entity\AdvertisementTypeConfiguration
 */
class AdvertisementTypeConfiguration
{
    /**
     * @var integer $id
     */
    private $id;

    /**
     * @var HealthCareAbroad\AdvertisementBundle\Entity\AdvertisementPropertyName
     */
    private $advertisementPropertyName;

    /**
     * @var HealthCareAbroad\AdvertisementBundle\Entity\AdvertisementType
     */
    private $advertisementType;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set advertisementPropertyName
     *
     * @param HealthCareAbroad\AdvertisementBundle\Entity\AdvertisementPropertyName $advertisementPropertyName
     * @return AdvertisementTypeConfiguration
     */
    public function setAdvertisementPropertyName(\HealthCareAbroad\AdvertisementBundle\Entity\AdvertisementPropertyName $advertisementPropertyName = null)
    {
        $this->advertisementPropertyName = $advertisementPropertyName;
        return $this;
    }

    /**
     * Get advertisementPropertyName
     *
     * @return HealthCareAbroad\AdvertisementBundle\Entity\AdvertisementPropertyName 
     */
    public function getAdvertisementPropertyName()
    {
        return $this->advertisementPropertyName;
    }

    /**
     * Set advertisementType
     *
     * @param HealthCareAbroad\AdvertisementBundle\Entity\AdvertisementType $advertisementType
     * @return AdvertisementTypeConfiguration
     */
    public function setAdvertisementType(\HealthCareAbroad\AdvertisementBundle\Entity\AdvertisementType $advertisementType = null)
    {
        $this->advertisementType = $advertisementType;
        return $this;
    }

    /**
     * Get advertisementType
     *
     * @return HealthCareAbroad\AdvertisementBundle\Entity\AdvertisementType 
     */
    public function getAdvertisementType()
    {
        return $this->advertisementType;
    }
}