<?php

namespace HealthCareAbroad\InstitutionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalProcedure
 */
class InstitutionMedicalProcedure
{
	const STATUS_ACTIVE = 1;
	
	const STATUS_INACTIVE = 0;
	
    /**
     * @var integer $institutionMedicalProcedureTypeId
     */
    private $institutionMedicalProcedureTypeId;

    /**
     * @var integer $medicalProcedureId
     */
    private $medicalProcedureId;

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
     * @var smallint $status
     */
    private $status;

    /**
     * @var HealthCareAbroad\MedicalProcedureBundle\Entity\MedicalProcedure
     */
    private $medicalProcedure;

    /**
     * @var HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalProcedureType
     */
    private $institutionMedicalProcedureType;


    /**
     * Get institutionMedicalProcedureTypeId
     *
     * @return integer 
     */
    public function getInstitutionMedicalProcedureTypeId()
    {
        return $this->institutionMedicalProcedureTypeId;
    }

    /**
     * Get medicalProcedureId
     *
     * @return integer 
     */
    public function getMedicalProcedureId()
    {
        return $this->medicalProcedureId;
    }

    /**
     * Set description
     *
     * @param text $description
     * @return InstitutionMedicalProcedure
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
     * @return InstitutionMedicalProcedure
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
     * @return InstitutionMedicalProcedure
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
     * @param smallint $status
     * @return InstitutionMedicalProcedure
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
     * Set medicalProcedure
     *
     * @param HealthCareAbroad\MedicalProcedureBundle\Entity\MedicalProcedure $medicalProcedure
     * @return InstitutionMedicalProcedure
     */
    public function setMedicalProcedure(\HealthCareAbroad\MedicalProcedureBundle\Entity\MedicalProcedure $medicalProcedure = null)
    {
        $this->medicalProcedure = $medicalProcedure;
        $this->setMedicalProcedureId($medicalProcedure->getId());
        return $this;
    }

    /**
     * Get medicalProcedure
     *
     * @return HealthCareAbroad\MedicalProcedureBundle\Entity\MedicalProcedure 
     */
    public function getMedicalProcedure()
    {
        return $this->medicalProcedure;
    }

    /**
     * Set institutionMedicalProcedureType
     *
     * @param HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalProcedureType $institutionMedicalProcedureType
     * @return InstitutionMedicalProcedure
     */
    public function setInstitutionMedicalProcedureType(\HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalProcedureType $institutionMedicalProcedureType = null)
    {
        $this->institutionMedicalProcedureType = $institutionMedicalProcedureType;
        $this->setInstitutionMedicalProcedureTypeId($institutionMedicalProcedureType->getId());
        return $this;
    }

    /**
     * Get institutionMedicalProcedureType
     *
     * @return HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalProcedureType 
     */
    public function getInstitutionMedicalProcedureType()
    {
        return $this->institutionMedicalProcedureType;
    }

    /**
     * Set institutionMedicalProcedureTypeId
     *
     * @param integer $institutionMedicalProcedureTypeId
     * @return InstitutionMedicalProcedure
     */
    public function setInstitutionMedicalProcedureTypeId($institutionMedicalProcedureTypeId)
    {
        $this->institutionMedicalProcedureTypeId = $institutionMedicalProcedureTypeId;
        return $this;
    }

    /**
     * Set medicalProcedureId
     *
     * @param integer $medicalProcedureId
     * @return InstitutionMedicalProcedure
     */
    public function setMedicalProcedureId($medicalProcedureId)
    {
        $this->medicalProcedureId = $medicalProcedureId;
        return $this;
    }
}