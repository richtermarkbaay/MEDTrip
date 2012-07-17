<?php

namespace HealthCareAbroad\ListingBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
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
     * @ORM\Column(type="string", nullable=true)
     * @var string $logo
     */
    private $logo;

    /**
     * @var datetime $dateModified
     */
    private $dateModified;

    /**
     * @var datetime $dateCreated
     */
    private $dateCreated;

    /**
     * @var boolean $status
     */
    private $status;

    /**
     * @var HealthCareAbroad\ProviderBundle\Entity\Provider
     */
    private $provider;

    /**
     * @var HealthCareAbroad\ProcedureBundle\Entity\MedicalProcedure
     */
    private $procedure;

    /**
     * @var ArrayCollection
     */
    private $locations;
    
    public function __construct()
    {
    	$this->locations = new ArrayCollection();
    }
    
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
     * Set logo
     *
     * @param string $logo
     * @return Listing
     */
    public function setLogo($logo)
    {
        $this->logo = $logo;
        return $this;
    }

    /**
     * Get logo
     *
     * @return string 
     */
    public function getLogo()
    {
        return $this->logo;
    }

    /**
     * Set dateModified
     *
     * @param datetime $dateModified
     * @return Listing
     */
    public function setDateModified($dateModified)
    {
        $this->dateModified = $dateModified;
        return $this;
    }

    /**
     * Get dateModified
     *
     * @return datetime 
     */
    public function getDateModified()
    {
        return $this->dateModified;
    }

    /**
     * Set dateCreated
     *
     * @param datetime $dateCreated
     * @return Listing
     */
    public function setDateCreated($dateCreated)
    {
        $this->dateCreated = $dateCreated;
        return $this;
    }

    /**
     * Get dateCreated
     *
     * @return datetime 
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
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
     * @return HealthCareAbroad\ProviderBundle\Entity\Provider 
     */
    public function getProvider()
    {
        return $this->provider;
    }

    /**
     * Set procedure
     *
     * @param HealthCareAbroad\ProcedureBundle\Entity\MedicalProcedure $procedure
     * @return Listing
     */
    public function setProcedure(\HealthCareAbroad\ProcedureBundle\Entity\MedicalProcedure $procedure = null)
    {
        $this->procedure = $procedure;
        return $this;
    }

    /**
     * Get procedure
     *
     * @return HealthCareAbroad\ProcedureBundle\Entity\MedicalProcedure 
     */
    public function getProcedure()
    {
        return $this->procedure;
    }

    /**
     * Set locations
     *
     * @param ArrayCollection $locations
     * @return locations
     */
    public function setLocations(ArrayCollection $locations)
    {
		foreach ($locations as $each) {
			$each->setListing($this);
		}

		$this->locations = $locations;
    }

    /**
     * get locations
     *
     * @return Listing
     */
    public function getLocations()
    {
    	return $this->locations;
    }
}