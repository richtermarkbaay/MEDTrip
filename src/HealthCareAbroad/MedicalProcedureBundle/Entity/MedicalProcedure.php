<?php

namespace HealthCareAbroad\MedicalProcedureBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HealthCareAbroad\MedicalProcedureBundle\Entity\MedicalProcedure
 */
class MedicalProcedure
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
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $institutionMedicalProcedures;

    /**
     * @var HealthCareAbroad\MedicalProcedureBundle\Entity\MedicalProcedureType
     */
    private $medicalProcedureType;

    public function __construct()
    {
        $this->institutionMedicalProcedures = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return MedicalProcedure
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
     * @return MedicalProcedure
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
     * @return MedicalProcedure
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
     * Add institutionMedicalProcedures
     *
     * @param HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalProcedure $institutionMedicalProcedures
     * @return MedicalProcedure
     */
    public function addInstitutionMedicalProcedure(\HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalProcedure $institutionMedicalProcedures)
    {
        $this->institutionMedicalProcedures[] = $institutionMedicalProcedures;
        return $this;
    }

    /**
     * Remove institutionMedicalProcedures
     *
     * @param HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalProcedure $institutionMedicalProcedures
     */
    public function removeInstitutionMedicalProcedure(\HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalProcedure $institutionMedicalProcedures)
    {
        $this->institutionMedicalProcedures->removeElement($institutionMedicalProcedures);
    }

    /**
     * Get institutionMedicalProcedures
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getInstitutionMedicalProcedures()
    {
        return $this->institutionMedicalProcedures;
    }

    /**
     * Set medicalProcedureType
     *
     * @param HealthCareAbroad\MedicalProcedureBundle\Entity\MedicalProcedureType $medicalProcedureType
     * @return MedicalProcedure
     */
    public function setMedicalProcedureType(\HealthCareAbroad\MedicalProcedureBundle\Entity\MedicalProcedureType $medicalProcedureType = null)
    {
        $this->medicalProcedureType = $medicalProcedureType;
        return $this;
    }

    /**
     * Get medicalProcedureType
     *
     * @return HealthCareAbroad\MedicalProcedureBundle\Entity\MedicalProcedureType 
     */
    public function getMedicalProcedureType()
    {
        return $this->medicalProcedureType;
    }
}