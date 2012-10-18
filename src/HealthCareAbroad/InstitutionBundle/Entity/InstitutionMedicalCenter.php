<?php

namespace HealthCareAbroad\InstitutionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter
 */
class InstitutionMedicalCenter
{
    const STATUS_ACTIVE = 1;
    
    const STATUS_INACTIVE = 0;
    
    /**
     * @var bigint $id
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
     * @var HealthCareAbroad\MedicalProcedureBundle\Entity\MedicalCenter
     */
    private $medicalCenter;

    /**
     * @var HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenterGroup
     */
    private $institutionMedicalCenterGroup;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $treatmentProcedures;

    public function __construct()
    {
        $this->treatmentProcedures = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Get id
     *
     * @return bigint 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set description
     *
     * @param text $description
     * @return InstitutionMedicalCenter
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
     * @return InstitutionMedicalCenter
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
     * @return InstitutionMedicalCenter
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
     * @return InstitutionMedicalCenter
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
     * Set medicalCenter
     *
     * @param HealthCareAbroad\MedicalProcedureBundle\Entity\MedicalCenter $medicalCenter
     * @return InstitutionMedicalCenter
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

    /**
     * Set institutionMedicalCenterGroup
     *
     * @param HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenterGroup $institutionMedicalCenterGroup
     * @return InstitutionMedicalCenter
     */
    public function setInstitutionMedicalCenterGroup(\HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenterGroup $institutionMedicalCenterGroup = null)
    {
        $this->institutionMedicalCenterGroup = $institutionMedicalCenterGroup;
        return $this;
    }

    /**
     * Get institutionMedicalCenterGroup
     *
     * @return HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenterGroup 
     */
    public function getInstitutionMedicalCenterGroup()
    {
        return $this->institutionMedicalCenterGroup;
    }

    /**
     * Add treatmentProcedures
     *
     * @param HealthCareAbroad\MedicalProcedureBundle\Entity\TreatmentProcedure $treatmentProcedures
     * @return InstitutionMedicalCenter
     */
    public function addTreatmentProcedure(\HealthCareAbroad\MedicalProcedureBundle\Entity\TreatmentProcedure $treatmentProcedures)
    {
        $this->treatmentProcedures[] = $treatmentProcedures;
        return $this;
    }

    /**
     * Remove treatmentProcedures
     *
     * @param HealthCareAbroad\MedicalProcedureBundle\Entity\TreatmentProcedure $treatmentProcedures
     */
    public function removeTreatmentProcedure(\HealthCareAbroad\MedicalProcedureBundle\Entity\TreatmentProcedure $treatmentProcedures)
    {
        $this->treatmentProcedures->removeElement($treatmentProcedures);
    }

    /**
     * Get treatmentProcedures
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getTreatmentProcedures()
    {
        return $this->treatmentProcedures;
    }
}