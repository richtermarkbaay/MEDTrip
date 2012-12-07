<?php

namespace HealthCareAbroad\AdvertisementBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HealthCareAbroad\AdvertisementBundle\Entity\AdvertisementPropertyValue
 */
class AdvertisementPropertyValue
{
    /**
     * @var bigint $id
     */
    private $id;

    /**
     * @var text $value
     */
    private $value;

    /**
     * @var HealthCareAbroad\AdvertisementBundle\Entity\AdvertisementPropertyName
     */
    private $advertisementPropertyName;

    /**
     * @var HealthCareAbroad\AdvertisementBundle\Entity\Advertisement
     */
    private $advertisement;


    /**
     * Get id
     *
     * @return bigint 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set value
     *
     * @param text $value
     * @return AdvertisementPropertyValue
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * Get value
     *
     * @return text 
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set advertisementPropertyName
     *
     * @param HealthCareAbroad\AdvertisementBundle\Entity\AdvertisementPropertyName $advertisementPropertyName
     * @return AdvertisementPropertyValue
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
     * Set advertisement
     *
     * @param HealthCareAbroad\AdvertisementBundle\Entity\Advertisement $advertisement
     * @return AdvertisementPropertyValue
     */
    public function setAdvertisement(\HealthCareAbroad\AdvertisementBundle\Entity\Advertisement $advertisement = null)
    {
        $this->advertisement = $advertisement;
        return $this;
    }

    /**
     * Get advertisement
     *
     * @return HealthCareAbroad\AdvertisementBundle\Entity\Advertisement 
     */
    public function getAdvertisement()
    {
        return $this->advertisement;
    }
}