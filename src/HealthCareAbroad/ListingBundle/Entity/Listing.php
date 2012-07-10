<?php

namespace HealthCareAbroad\ListingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HealthCareAbroad\ListingBundle\Entity\Listing
 */
class Listing
{
    /**
     * @var bigint $id
     */
    private $id;

    /**
     * @var string $title
     */
    private $title;

    /**
     * @var text $description
     */
    private $description;

    /**
     * @var boolean $dateModified
     */
    private $dateModified;
    
    /**
     * @var boolean $dateCreated
     */
    private $dateCreated;
    
    /**
     * @var boolean $status
     */
    private $status;

    /**
     * @var HealthCareAbroad\ListingBundle\Entity\Provider
     */
    private $provider;

    /**
     * @var text $country
     */
    //private $country;

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
     * Set title
     *
     * @param string $title
     * @return Listing
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set description
     *
     * @param text $description
     * @return Listing
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Get description
     *
     * @return text 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set date_modified
     *
     * @param datetime $dateModified
     * @return Listing
     */
    public function setDateModified($dateModified)
    {
    	$this->date_modified = $dateModified;
    	return $this;
    }
    
    /**
     * Get date_modified
     *
     * @return datetime
     */
    public function getDateModified()
    {
    	return $this->date_modified;
    }

    /**
     * Set date_created
     *
     * @param datetime $dateCreated
     * @return Listing
     */
    public function setDateCreated($dateCreated)
    {
    	$this->date_created = $dateCreated;
    	return $this;
    }
    
    /**
     * Get date_created
     *
     * @return datetime
     */
    public function getDateCreated()
    {
    	return $this->date_created;
    }

    /**
     * Set status
     *
     * @param boolean $status
     * @return Listing
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
     * Set provider
     *
     * @param HealthCareAbroad\ProviderBundle\Entity\Provider $provider
     * @return Listing
     */
    public function setProvider(\HealthCareAbroad\ProviderBundle\Entity\Provider $provider = null)
    {
        $this->provider = $provider;
        return $this;
    }

    /**
     * Get provider
     *
     * @return HealthCareAbroad\ListingBundle\Entity\Provider 
     */
    public function getProvider()
    {
        return $this->provider;
    }
}