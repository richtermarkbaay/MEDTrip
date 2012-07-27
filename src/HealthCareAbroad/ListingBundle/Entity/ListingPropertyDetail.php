<?php

namespace HealthCareAbroad\ListingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HealthCareAbroad\ListingBundle\Entity\ListingPropertyDetail
 */
class ListingPropertyDetail
{
    /**
     * @var string $value
     */
    private $value;

    /**
     * @var boolean $status
     */
    private $status;

    /**
     * @var HealthCareAbroad\ListingBundle\Entity\Listing
     */
    private $listing;

    /**
     * @var HealthCareAbroad\ListingBundle\Entity\ListingProperty
     */
    private $listingProperty;

    /**
     * @var HealthCareAbroad\ListingBundle\Entity\ListingPropertyChoice
     */
    private $listingPropertyChoice;


    /**
     * Set value
     *
     * @param string $value
     * @return ListingPropertyDetail
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
     * @return ListingPropertyDetail
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
     * Set listing
     *
     * @param HealthCareAbroad\ListingBundle\Entity\Listing $listing
     * @return ListingPropertyDetail
     */
    public function setListing(\HealthCareAbroad\ListingBundle\Entity\Listing $listing = null)
    {
        $this->listing = $listing;
        return $this;
    }

    /**
     * Get listing
     *
     * @return HealthCareAbroad\ListingBundle\Entity\Listing 
     */
    public function getListing()
    {
        return $this->listing;
    }

    /**
     * Set listingProperty
     *
     * @param HealthCareAbroad\ListingBundle\Entity\ListingProperty $listingProperty
     * @return ListingPropertyDetail
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

    /**
     * Set listingPropertyChoice
     *
     * @param HealthCareAbroad\ListingBundle\Entity\ListingPropertyChoice $listingPropertyChoice
     * @return ListingPropertyDetail
     */
    public function setListingPropertyChoice(\HealthCareAbroad\ListingBundle\Entity\ListingPropertyChoice $listingPropertyChoice = null)
    {
        $this->listingPropertyChoice = $listingPropertyChoice;
        return $this;
    }

    /**
     * Get listingPropertyChoice
     *
     * @return HealthCareAbroad\ListingBundle\Entity\ListingPropertyChoice 
     */
    public function getListingPropertyChoice()
    {
        return $this->listingPropertyChoice;
    }
    /**
     * @var bigint $listingId
     */
    private $listingId;

    /**
     * @var bigint $listingPropertyId
     */
    private $listingPropertyId;

    /**
     * @var bigint $listingPropertyChoiceId
     */
    private $listingPropertyChoiceId;


    /**
     * Set listingId
     *
     * @param bigint $listingId
     * @return ListingPropertyDetail
     */
    public function setListingId($listingId)
    {
        $this->listingId = $listingId;
        return $this;
    }

    /**
     * Get listingId
     *
     * @return bigint 
     */
    public function getListingId()
    {
        return $this->listingId;
    }

    /**
     * Set listingPropertyId
     *
     * @param bigint $listingPropertyId
     * @return ListingPropertyDetail
     */
    public function setListingPropertyId($listingPropertyId)
    {
        $this->listingPropertyId = $listingPropertyId;
        return $this;
    }

    /**
     * Get listingPropertyId
     *
     * @return bigint 
     */
    public function getListingPropertyId()
    {
        return $this->listingPropertyId;
    }

    /**
     * Set listingPropertyChoiceId
     *
     * @param bigint $listingPropertyChoiceId
     * @return ListingPropertyDetail
     */
    public function setListingPropertyChoiceId($listingPropertyChoiceId)
    {
        $this->listingPropertyChoiceId = $listingPropertyChoiceId;
        return $this;
    }

    /**
     * Get listingPropertyChoiceId
     *
     * @return bigint 
     */
    public function getListingPropertyChoiceId()
    {
        return $this->listingPropertyChoiceId;
    }
}