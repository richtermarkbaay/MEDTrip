<?php
namespace HealthCareAbroad\ListingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

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
     * @var boolean $status
     */
    private $status;

    /**
     * @var HealthCareAbroad\ListingBundle\Entity\Provider
     */
    private $provider;

    

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
     * @param HealthCareAbroad\ListingBundle\Entity\Provider $provider
     * @return Listing
     */
    public function setProvider(\HealthCareAbroad\ListingBundle\Entity\Provider $provider = null)
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
    
    private $listingProperties;
    
    
    public function __construct() 
    {
    	$this->listingProperties = new ArrayCollection();	
    }
}