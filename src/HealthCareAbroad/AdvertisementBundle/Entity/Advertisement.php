<?php
namespace HealthCareAbroad\AdvertisementBundle\Entity;

use HealthCareAbroad\HelperBundle\Classes\CommonArrayAccess;

// TODO - extending CommonArrayAccess which implements ArrayAccess is a temporary fix for expanded: true 
class Advertisement
{
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

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
     * @var datetime $dateCreated
     */
    private $dateCreated;

    /**
     * @var boolean $status
     */
    private $status;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */ 
    private $advertisementPropertyValues;

    /**
     * @var HealthCareAbroad\AdvertisementBundle\Entity\AdvertisementType
     */
    private $advertisementType;

    /**
     * @var HealthCareAbroad\InstitutionBundle\Entity\Institution
     */
    private $institution;

    public function __construct()
    {
        $this->advertisementPropertyValues = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return Advertisement
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
     * @return Advertisement
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
     * Set dateCreated
     *
     * @param datetime $dateCreated
     * @return Advertisement
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
     * @return Advertisement
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
     * Add advertisementPropertyValues
     *
     * @param HealthCareAbroad\AdvertisementBundle\Entity\AdvertisementPropertyValue $advertisementPropertyValues
     * @return Advertisement
     */
    public function addAdvertisementPropertyValue(\HealthCareAbroad\AdvertisementBundle\Entity\AdvertisementPropertyValue $advertisementPropertyValues)
    {
        $this->advertisementPropertyValues[] = $advertisementPropertyValues;
        return $this;
    }

    /**
     * Remove advertisementPropertyValues
     *
     * @param HealthCareAbroad\AdvertisementBundle\Entity\AdvertisementPropertyValue $advertisementPropertyValues
     */
    public function removeAdvertisementPropertyValue(\HealthCareAbroad\AdvertisementBundle\Entity\AdvertisementPropertyValue $advertisementPropertyValues)
    {
        $this->advertisementPropertyValues->removeElement($advertisementPropertyValues);
    }

    /**
     * Get advertisementPropertyValues
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getAdvertisementPropertyValues()
    {
        return $this->advertisementPropertyValues;
    }

    /**
     * Set advertisementType
     *
     * @param HealthCareAbroad\AdvertisementBundle\Entity\AdvertisementType $advertisementType
     * @return Advertisement
     */
    public function setAdvertisementType(\HealthCareAbroad\AdvertisementBundle\Entity\AdvertisementType $advertisementType = null)
    {
        $this->advertisementType = $advertisementType;
        return $this;
    }

    /**
     * Get advertisementType
     *
     * @return HealthCareAbroad\AdvertisementBundle\Entity\AdvertisementType 
     */
    public function getAdvertisementType()
    {
        return $this->advertisementType;
    }

    /**
     * Set institution
     *
     * @param HealthCareAbroad\InstitutionBundle\Entity\Institution $institution
     * @return Advertisement
     */
    public function setInstitution(\HealthCareAbroad\InstitutionBundle\Entity\Institution $institution = null)
    {
        $this->institution = $institution;
        return $this;
    }

    /**
     * Get institution
     *
     * @return HealthCareAbroad\InstitutionBundle\Entity\Institution 
     */
    public function getInstitution()
    {
        return $this->institution;
    }
}