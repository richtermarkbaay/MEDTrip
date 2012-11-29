<?php

namespace HealthCareAbroad\AdvertisementBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HealthCareAbroad\AdvertisementBundle\Entity\AdvertisementPropertyName
 */
class AdvertisementPropertyName
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
     * @var string $label
     */
    private $label;

    /**
     * @var string $dataClass
     */
    private $dataClass;

    /**
     * @var string $propertyConfig
     */
    private $propertyConfig;

    /**
     * @var smallint $status
     */
    private $status;

    /**
     * @var HealthCareAbroad\HelperBundle\Entity\DataType
     */
    private $dataType;


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
     * @return AdvertisementPropertyName
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
     * Set label
     *
     * @param string $label
     * @return AdvertisementPropertyName
     */
    public function setLabel($label)
    {
        $this->label = $label;
        return $this;
    }

    /**
     * Get label
     *
     * @return string 
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Set dataClass
     *
     * @param string $dataClass
     * @return AdvertisementPropertyName
     */
    public function setDataClass($dataClass)
    {
        $this->dataClass = $dataClass;
        return $this;
    }

    /**
     * Get dataClass
     *
     * @return string 
     */
    public function getDataClass()
    {
        return $this->dataClass;
    }

    /**
     * Set propertyConfig
     *
     * @param string $propertyConfig
     * @return AdvertisementPropertyName
     */
    public function setPropertyConfig($propertyConfig)
    {
        $this->propertyConfig = $propertyConfig;
        return $this;
    }

    /**
     * Get propertyConfig
     *
     * @return string 
     */
    public function getPropertyConfig()
    {
        return $this->propertyConfig;
    }

    /**
     * Set status
     *
     * @param smallint $status
     * @return AdvertisementPropertyName
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
     * Set dataType
     *
     * @param HealthCareAbroad\HelperBundle\Entity\DataType $dataType
     * @return AdvertisementPropertyName
     */
    public function setDataType(\HealthCareAbroad\HelperBundle\Entity\DataType $dataType = null)
    {
        $this->dataType = $dataType;
        return $this;
    }

    /**
     * Get dataType
     *
     * @return HealthCareAbroad\HelperBundle\Entity\DataType 
     */
    public function getDataType()
    {
        return $this->dataType;
    }
}