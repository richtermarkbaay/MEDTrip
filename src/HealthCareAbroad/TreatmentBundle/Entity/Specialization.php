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
     * @var integer $id
     */
    private $id;

    /**
     * @var string $name
     */
    private $name;

    /**
     * @var text $description
     */
    private $description;

    /**
     * @var datetime $dateCreated
     */
    private $dateCreated;

    /**
     * @var string $slug
     */
    private $slug;

    /**
     * @var smallint $status
     */
    private $status;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $subSpecializations;

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
     * @param text $description
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
     * @return datetime
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
     * @param smallint $status
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
     * @return smallint
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Add subSpecializations
     *
     * @param HealthCareAbroad\TreatmentBundle\Entity\SubSpecialization $subSpecializations
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

    public function __toString()
    {
        return $this->name;
    }
}