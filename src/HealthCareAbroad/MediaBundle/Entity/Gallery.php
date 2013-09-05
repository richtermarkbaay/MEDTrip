<?php

namespace HealthCareAbroad\MediaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HealthCareAbroad\MediaBundle\Entity\Gallery
 */
class Gallery
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var \DateTime
     */
    private $dateCreated;

    /**
     * @var \HealthCareAbroad\InstitutionBundle\Entity\Institution
     */
    private $institution;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $media;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->media = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set dateCreated
     *
     * @param \DateTime $dateCreated
     * @return Gallery
     */
    public function setDateCreated($dateCreated)
    {
        $this->dateCreated = $dateCreated;
    
        return $this;
    }

    /**
     * Get dateCreated
     *
     * @return \DateTime 
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    /**
     * Set institution
     *
     * @param \HealthCareAbroad\InstitutionBundle\Entity\Institution $institution
     * @return Gallery
     */
    public function setInstitution(\HealthCareAbroad\InstitutionBundle\Entity\Institution $institution = null)
    {
        $this->institution = $institution;
    
        return $this;
    }

    /**
     * Get institution
     *
     * @return \HealthCareAbroad\InstitutionBundle\Entity\Institution 
     */
    public function getInstitution()
    {
        return $this->institution;
    }

    /**
     * Add media
     *
     * @param \HealthCareAbroad\MediaBundle\Entity\Media $media
     * @return Gallery
     */
    public function addMedia(\HealthCareAbroad\MediaBundle\Entity\Media $media)
    {
        $this->media[] = $media;
    
        return $this;
    }

    /**
     * Remove media
     *
     * @param \HealthCareAbroad\MediaBundle\Entity\Media $media
     */
    public function removeMedia(\HealthCareAbroad\MediaBundle\Entity\Media $media)
    {
        $this->media->removeElement($media);
    }

    /**
     * Get media
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMedia()
    {
        return $this->media;
    }
}