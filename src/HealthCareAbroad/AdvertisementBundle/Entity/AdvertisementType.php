<?php

namespace HealthCareAbroad\AdvertisementBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HealthCareAbroad\AdvertisementBundle\Entity\AdvertisementType
 */
class AdvertisementType
{
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

    /**
     * @var smallint $id
     */
    private $id;

    /**
     * @var string $name
     */
    private $name;

    /**
     * @var smallint $status
     */
    private $status;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $advertisementTypeConfigurations;

    public function __construct()
    {
        $this->advertisementTypeConfigurations = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Get id
     *
     * @return smallint 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return AdvertisementType
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set status
     *
     * @param smallint $status
     * @return AdvertisementType
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * Get status
     *
     * @return smallint 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Add advertisementTypeConfigurations
     *
     * @param HealthCareAbroad\AdvertisementBundle\Entity\AdvertisementPropertyName $advertisementTypeConfigurations
     * @return AdvertisementType
     */
    public function addAdvertisementTypeConfiguration(\HealthCareAbroad\AdvertisementBundle\Entity\AdvertisementPropertyName $advertisementTypeConfigurations)
    {
        $this->advertisementTypeConfigurations[] = $advertisementTypeConfigurations;
        return $this;
    }

    /**
     * Remove advertisementTypeConfigurations
     *
     * @param HealthCareAbroad\AdvertisementBundle\Entity\AdvertisementPropertyName $advertisementTypeConfigurations
     */
    public function removeAdvertisementTypeConfiguration(\HealthCareAbroad\AdvertisementBundle\Entity\AdvertisementPropertyName $advertisementTypeConfigurations)
    {
        $this->advertisementTypeConfigurations->removeElement($advertisementTypeConfigurations);
    }

    /**
     * Get advertisementTypeConfigurations
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getAdvertisementTypeConfigurations()
    {
        return $this->advertisementTypeConfigurations;
    }
}