<?php
namespace HealthCareAbroad\AdvertisementBundle\Entity;

abstract class Advertisement
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
     * @var datetime $dateCreated
     */
    private $dateCreated;

    /**
     * @var boolean $status
     */
    private $status;

    /**
     * @var HealthCareAbroad\AdvertisementBundle\Entity\AdvertisementType
     */
    private $advertisementType;

    /**
     * @var HealthCareAbroad\AdvertisementBundle\Entity\Institution
     */
    private $institution;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $media;

    public function __construct()
    {
        $this->media = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @param HealthCareAbroad\AdvertisementBundle\Entity\Institution $institution
     * @return Advertisement
     */
    public function setInstitution(\HealthCareAbroad\AdvertisementBundle\Entity\Institution $institution = null)
    {
        $this->institution = $institution;
        return $this;
    }

    /**
     * Get institution
     *
     * @return HealthCareAbroad\AdvertisementBundle\Entity\Institution 
     */
    public function getInstitution()
    {
        return $this->institution;
    }

    /**
     * Add media
     *
     * @param HealthCareAbroad\MediaBundle\Entity\Media $media
     * @return Advertisement
     */
    public function addMedia(\HealthCareAbroad\MediaBundle\Entity\Media $media)
    {
        $this->media[] = $media;
        return $this;
    }

    /**
     * Remove media
     *
     * @param HealthCareAbroad\MediaBundle\Entity\Media $media
     */
    public function removeMedia(\HealthCareAbroad\MediaBundle\Entity\Media $media)
    {
        $this->media->removeElement($media);
    }

    /**
     * Get media
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getMedia()
    {
        return $this->media;
    }
}