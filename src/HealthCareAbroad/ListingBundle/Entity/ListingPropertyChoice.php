<?php

namespace HealthCareAbroad\ListingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HealthCareAbroad\ListingBundle\Entity\ListingPropertyChoice
 */
class ListingPropertyChoice
{
    /**
     * @var bigint $id
     */
    private $id;

    /**
     * @var string $value
     */
    private $value;

    /**
     * @var boolean $status
     */
    private $status;

    /**
     * @var HealthCareAbroad\ListingBundle\Entity\ListingProperty
     */
    private $listingProperty;


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
     * @param string $value
     * @return ListingPropertyChoice
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * Get value
     *
     * @return string 
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set status
     *
     * @param boolean $status
     * @return ListingPropertyChoice
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * Get status
     *
     * @return boolean 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set listingProperty
     *
     * @param HealthCareAbroad\ListingBundle\Entity\ListingProperty $listingProperty
     * @return ListingPropertyChoice
     */
    public function setListingProperty(\HealthCareAbroad\ListingBundle\Entity\ListingProperty $listingProperty = null)
    {
        $this->listingProperty = $listingProperty;
        return $this;
    }

    /**
     * Get listingProperty
     *
     * @return HealthCareAbroad\ListingBundle\Entity\ListingProperty 
     */
    public function getListingProperty()
    {
        return $this->listingProperty;
    }
}