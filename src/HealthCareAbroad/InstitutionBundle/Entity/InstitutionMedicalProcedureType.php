<?php
namespace HealthCareAbroad\InstitutionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalProcedureType
 */
class InstitutionMedicalProcedureType
{
    const STATUS_ACTIVE = 1;
    
    const STATUS_INACTIVE = 0;
    
    /**
     * @var integer $id
     */
    private $id;

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
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $institutionMedicalProcedures;

    /**
     * @var HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter
     */
    private $institutionMedicalCenter;

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
     * Set description
     *
     * @param text $description
     * @return InstitutionMedicalProcedureType
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
     * @return InstitutionMedicalProcedureType
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
     * @return InstitutionMedicalProcedureType
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
     * @return InstitutionMedicalProcedureType
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
     * @return InstitutionMedicalProcedureType
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
     * Set institutionMedicalCenter
     *
     * @param HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter $institutionMedicalCenter
     * @return InstitutionMedicalProcedureType
     */
    public function setInstitutionMedicalCenter(\HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter $institutionMedicalCenter = null)
    {
        $this->institutionMedicalCenter = $institutionMedicalCenter;
        return $this;
    }

    /**
     * Get institutionMedicalCenter
     *
     * @return HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter 
     */
    public function getInstitutionMedicalCenter()
    {
        return $this->institutionMedicalCenter;
    }

    /**
     * Set medicalProcedureType
     *
     * @param HealthCareAbroad\MedicalProcedureBundle\Entity\MedicalProcedureType $medicalProcedureType
     * @return InstitutionMedicalProcedureType
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