<?php

namespace HealthCareAbroad\ListingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HealthCareAbroad\ListingBundle\Entity\ListingLocation
 */
class ListingLocation
{
    /**
     * @var bigint $id
     */
    private $id;

    /**
     * @var text $address
     */
    private $address;

    /**
     * @var HealthCareAbroad\HelperBundle\Entity\City
     */
    private $city;

    /**
     * @var HealthCareAbroad\ListingBundle\Entity\Listing
     */
    private $listing;

    /**
     * @var HealthCareAbroad\HelperBundle\Entity\Country
     */
    private $country;


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
     * Set address
     *
     * @param text $address
     * @return ListingLocation
     */
    public function setAddress($address)
    {
        $this->address = $address;
        return $this;
    }

    /**
     * Get address
     *
     * @return text 
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set city
     *
     * @param HealthCareAbroad\HelperBundle\Entity\City $city
     * @return ListingLocation
     */
    public function setCity(\HealthCareAbroad\HelperBundle\Entity\City $city = null)
    {
        $this->city = $city;
        return $this;
    }

    /**
     * Get city
     *
     * @return HealthCareAbroad\HelperBundle\Entity\City 
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set listing
     *
     * @param HealthCareAbroad\ListingBundle\Entity\Listing $listing
     * @return ListingLocation
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
     * Set country
     *
     * @param HealthCareAbroad\HelperBundle\Entity\Country $country
     * @return ListingLocation
     */
    public function setCountry(\HealthCareAbroad\HelperBundle\Entity\Country $country = null)
    {
        $this->country = $country;
        return $this;
    }

    /**
     * Get country
     *
     * @return HealthCareAbroad\HelperBundle\Entity\Country 
     */
    public function getCountry()
    {
        return $this->country;
    }
}