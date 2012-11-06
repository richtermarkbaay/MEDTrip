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
    private $subSpecialization;

    public function __construct()
    {
        $this->subSpecialization = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Add subSpecialization
     *
     * @param HealthCareAbroad\TreatmentBundle\Entity\SubSpecialization $subSpecialization
     * @return Treatment
     */
    public function addSubSpecialization(\HealthCareAbroad\TreatmentBundle\Entity\SubSpecialization $subSpecialization)
    {
        $this->subSpecialization[] = $subSpecialization;
        return $this;
    }

    /**
     * Remove subSpecialization
     *
     * @param HealthCareAbroad\TreatmentBundle\Entity\SubSpecialization $subSpecialization
     */
    public function removeSubSpecialization(\HealthCareAbroad\TreatmentBundle\Entity\SubSpecialization $subSpecialization)
    {
        $this->subSpecialization->removeElement($subSpecialization);
    }

    /**
     * Get subSpecialization
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getSubSpecialization()
    {
        return $this->subSpecialization;
    }
    
    public function __toString()
    {
        return $this->name;
    }
}