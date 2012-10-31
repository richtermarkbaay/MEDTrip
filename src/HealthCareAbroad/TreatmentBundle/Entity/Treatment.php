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
     * @var HealthCareAbroad\TreatmentBundle\Entity\SubSpecialization
     */
    private $subSpecialization;

    public function __construct()
    {
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
     * Set sub-specialization
     *
     * @param HealthCareAbroad\TreatmentBundle\Entity\SubSpecialization $subSpecialization
     * @return TreatmentProcedure
     */
    public function setSubSpecialization(\HealthCareAbroad\TreatmentBundle\Entity\SubSpecialization $subSpecialization = null)
    {
        $this->subSpecialization = $subSpecialization;
        return $this;
    }

    /**
     * Get treatment
     *
     * @return HealthCareAbroad\TreatmentBundle\Entity\SubSpecialization
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