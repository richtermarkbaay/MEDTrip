<?php

namespace HealthCareAbroad\MedicalProcedureBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HealthCareAbroad\MedicalProcedureBundle\Entity\MedicalProcedureType
 */
class MedicalProcedureType
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
     * @var datetime $dateModified
     */
    private $dateModified;

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
    private $medicalProcedures;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $institutionMedicalProcedureTypes;

    /**
     * @var HealthCareAbroad\MedicalProcedureBundle\Entity\MedicalCenter
     */
    private $medicalCenter;

    public function __construct()
    {
        $this->medicalProcedures = new \Doctrine\Common\Collections\ArrayCollection();
        $this->institutionMedicalProcedureTypes = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return MedicalProcedureType
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
     * @return MedicalProcedureType
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
     * Set dateModified
     *
     * @param datetime $dateModified
     * @return MedicalProcedureType
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
     * @return MedicalProcedureType
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
     * @return MedicalProcedureType
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
     * @return MedicalProcedureType
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
     * Add medicalProcedures
     *
     * @param HealthCareAbroad\MedicalProcedureBundle\Entity\MedicalProcedure $medicalProcedures
     * @return MedicalProcedureType
     */
    public function addMedicalProcedure(\HealthCareAbroad\MedicalProcedureBundle\Entity\MedicalProcedure $medicalProcedures)
    {
        $this->medicalProcedures[] = $medicalProcedures;
        return $this;
    }

    /**
     * Remove medicalProcedures
     *
     * @param HealthCareAbroad\MedicalProcedureBundle\Entity\MedicalProcedure $medicalProcedures
     */
    public function removeMedicalProcedure(\HealthCareAbroad\MedicalProcedureBundle\Entity\MedicalProcedure $medicalProcedures)
    {
        $this->medicalProcedures->removeElement($medicalProcedures);
    }

    /**
     * Get medicalProcedures
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getMedicalProcedures()
    {
        return $this->medicalProcedures;
    }

    /**
     * Add institutionMedicalProcedureTypes
     *
     * @param HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalProcedureType $institutionMedicalProcedureTypes
     * @return MedicalProcedureType
     */
    public function addInstitutionMedicalProcedureType(\HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalProcedureType $institutionMedicalProcedureTypes)
    {
        $this->institutionMedicalProcedureTypes[] = $institutionMedicalProcedureTypes;
        return $this;
    }

    /**
     * Remove institutionMedicalProcedureTypes
     *
     * @param HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalProcedureType $institutionMedicalProcedureTypes
     */
    public function removeInstitutionMedicalProcedureType(\HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalProcedureType $institutionMedicalProcedureTypes)
    {
        $this->institutionMedicalProcedureTypes->removeElement($institutionMedicalProcedureTypes);
    }

    /**
     * Get institutionMedicalProcedureTypes
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getInstitutionMedicalProcedureTypes()
    {
        return $this->institutionMedicalProcedureTypes;
    }

    /**
     * Set medicalCenter
     *
     * @param HealthCareAbroad\MedicalProcedureBundle\Entity\MedicalCenter $medicalCenter
     * @return MedicalProcedureType
     */
    public function setMedicalCenter(\HealthCareAbroad\MedicalProcedureBundle\Entity\MedicalCenter $medicalCenter = null)
    {
        $this->medicalCenter = $medicalCenter;
        return $this;
    }

    /**
     * Get medicalCenter
     *
     * @return HealthCareAbroad\MedicalProcedureBundle\Entity\MedicalCenter 
     */
    public function getMedicalCenter()
    {
        return $this->medicalCenter;
    }
    
    public function __toString()
    {
        return $this->name;
    }
}