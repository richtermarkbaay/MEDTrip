<?php
namespace HealthCareAbroad\TreatmentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

class Treatment
{
    const STATUS_ACTIVE = 1;

    const STATUS_INACTIVE = 0;
    
    /**
     * @var integer $id
     */
    private $id;

    /**
     * @var string $name
     */
    private $name;

    /**
     * @var string $slug
     */
    private $slug;

    /**
     * @var smallint $status
     */
    private $status;

    /**
     * @var HealthCareAbroad\TreatmentBundle\Entity\Specialization
     */
    private $specialization;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $subSpecializations;
    
    /**
     * @var text $description
     */
    private $description;

    public function __construct()
    {
        $this->subSpecializations = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return Treatment
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
     * Set slug
     *
     * @param string $slug
     * @return Treatment
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
     * @param smallint $status
     * @return Treatment
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * Get status
     *
     * @return smallint 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set specialization
     *
     * @param HealthCareAbroad\TreatmentBundle\Entity\Specialization $specialization
     * @return Treatment
     */
    public function setSpecialization(\HealthCareAbroad\TreatmentBundle\Entity\Specialization $specialization = null)
    {
        $this->specialization = $specialization;
        return $this;
    }

    /**
     * Get specialization
     *
     * @return HealthCareAbroad\TreatmentBundle\Entity\Specialization 
     */
    public function getSpecialization()
    {
        return $this->specialization;
    }

    /**
     * Add subSpecializations
     *
     * @param HealthCareAbroad\TreatmentBundle\Entity\SubSpecialization $subSpecializations
     * @return Treatment
     */
    public function addSubSpecialization(\HealthCareAbroad\TreatmentBundle\Entity\SubSpecialization $subSpecializations)
    {
        $this->subSpecializations[] = $subSpecializations;
        return $this;
    }

    /**
     * Remove subSpecializations
     *
     * @param HealthCareAbroad\TreatmentBundle\Entity\SubSpecialization $subSpecializations
     */
    public function removeSubSpecialization(\HealthCareAbroad\TreatmentBundle\Entity\SubSpecialization $subSpecializations)
    {
        $this->subSpecializations->removeElement($subSpecializations);
    }

    /**
     * Get subSpecializations
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getSubSpecializations()
    {
        return $this->subSpecializations;
    }

    /**
     * Set description
     *
     * @param text $description
     * @return Treatment
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
    
    public function isActive()
    {
        return $this->status == self::STATUS_ACTIVE;
    }
    
    public function __toString()
    {
        return $this->name;
    }
}