<?php
namespace HealthCareAbroad\TreatmentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HealthCareAbroad\TreatmentBundle\Entity\Specialization
 */
class Specialization
{
    const STATUS_ACTIVE = 1;

    const STATUS_INACTIVE = 0;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $description;

    /**
     * @var \DateTime
     */
    private $dateCreated;

    /**
     * @var string
     */
    private $slug;

    /**
     * @var integer
     */
    private $status;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $treatments;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $subSpecializations;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $medicalSpecialities;

    /**
     * @var \HealthCareAbroad\MediaBundle\Entity\Media
     */
    private $media;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->treatments = new \Doctrine\Common\Collections\ArrayCollection();
        $this->subSpecializations = new \Doctrine\Common\Collections\ArrayCollection();
        $this->medicalSpecialities = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set name
     *
     * @param string $name
     * @return Specialization
     */
    public function setName($name)
    {
        $this->name = $name;
    
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Specialization
     */
    public function setDescription($description)
    {
        $this->description = $description;
    
        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set dateCreated
     *
     * @param \DateTime $dateCreated
     * @return Specialization
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
     * Set slug
     *
     * @param string $slug
     * @return Specialization
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
    
        return $this;
    }

    /**
     * Get slug
     *
     * @return string 
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return Specialization
     */
    public function setStatus($status)
    {
        $this->status = $status;
    
        return $this;
    }

    /**
     * Get status
     *
     * @return integer 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Add treatments
     *
     * @param \HealthCareAbroad\TreatmentBundle\Entity\Treatment $treatments
     * @return Specialization
     */
    public function addTreatment(\HealthCareAbroad\TreatmentBundle\Entity\Treatment $treatments)
    {
        $this->treatments[] = $treatments;
    
        return $this;
    }

    /**
     * Remove treatments
     *
     * @param \HealthCareAbroad\TreatmentBundle\Entity\Treatment $treatments
     */
    public function removeTreatment(\HealthCareAbroad\TreatmentBundle\Entity\Treatment $treatments)
    {
        $this->treatments->removeElement($treatments);
    }

    /**
     * Get treatments
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTreatments()
    {
        return $this->treatments;
    }

    /**
     * Add subSpecializations
     *
     * @param \HealthCareAbroad\TreatmentBundle\Entity\SubSpecialization $subSpecializations
     * @return Specialization
     */
    public function addSubSpecialization(\HealthCareAbroad\TreatmentBundle\Entity\SubSpecialization $subSpecializations)
    {
        $this->subSpecializations[] = $subSpecializations;
    
        return $this;
    }

    /**
     * Remove subSpecializations
     *
     * @param \HealthCareAbroad\TreatmentBundle\Entity\SubSpecialization $subSpecializations
     */
    public function removeSubSpecialization(\HealthCareAbroad\TreatmentBundle\Entity\SubSpecialization $subSpecializations)
    {
        $this->subSpecializations->removeElement($subSpecializations);
    }

    /**
     * Get subSpecializations
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSubSpecializations()
    {
        return $this->subSpecializations;
    }

    /**
     * Add medicalSpecialities
     *
     * @param \HealthCareAbroad\DoctorBundle\Entity\MedicalSpeciality $medicalSpecialities
     * @return Specialization
     */
    public function addMedicalSpecialitie(\HealthCareAbroad\DoctorBundle\Entity\MedicalSpeciality $medicalSpecialities)
    {
        $this->medicalSpecialities[] = $medicalSpecialities;
    
        return $this;
    }

    /**
     * Remove medicalSpecialities
     *
     * @param \HealthCareAbroad\DoctorBundle\Entity\MedicalSpeciality $medicalSpecialities
     */
    public function removeMedicalSpecialitie(\HealthCareAbroad\DoctorBundle\Entity\MedicalSpeciality $medicalSpecialities)
    {
        $this->medicalSpecialities->removeElement($medicalSpecialities);
    }

    /**
     * Get medicalSpecialities
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMedicalSpecialities()
    {
        return $this->medicalSpecialities;
    }

    /**
     * Set media
     *
     * @param \HealthCareAbroad\MediaBundle\Entity\Media $media
     * @return Specialization
     */
    public function setMedia(\HealthCareAbroad\MediaBundle\Entity\Media $media = null)
    {
        $this->media = $media;
    
        return $this;
    }

    /**
     * Get media
     *
     * @return \HealthCareAbroad\MediaBundle\Entity\Media 
     */
    public function getMedia()
    {
        return $this->media;
    }
    
    public function __toString()
    {
        return $this->name;
    }
}