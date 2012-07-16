<?php

namespace HealthCareAbroad\ListingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HealthCareAbroad\ListingBundle\Entity\ListingPhoto
 */
class ListingPhoto
{
    /**
     * @var bigint $id
     */
    private $id;

    /**
     * @var HealthCareAbroad\HelperBundle\Entity\Photo
     */
    private $photo;

    /**
     * @var HealthCareAbroad\ListingBundle\Entity\Listing
     */
    private $listing;


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
     * Set photo
     *
     * @param HealthCareAbroad\HelperBundle\Entity\Photo $photo
     * @return ListingPhoto
     */
    public function setPhoto(\HealthCareAbroad\HelperBundle\Entity\Photo $photo = null)
    {
        $this->photo = $photo;
        return $this;
    }

    /**
     * Get photo
     *
     * @return HealthCareAbroad\HelperBundle\Entity\Photo 
     */
    public function getPhoto()
    {
        return $this->photo;
    }

    /**
     * Set listing
     *
     * @param HealthCareAbroad\ListingBundle\Entity\Listing $listing
     * @return ListingPhoto
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
}