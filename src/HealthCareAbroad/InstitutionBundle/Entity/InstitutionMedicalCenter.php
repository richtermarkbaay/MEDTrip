<?php

namespace HealthCareAbroad\InstitutionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter
 */
class InstitutionMedicalCenter
{
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
     * @var HealthCareAbroad\InstitutionBundle\Entity\Institution
     */
    private $institution;
    
    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $institutionMedicalProcedureTypes;
    
    public function __construct()
    {
        $this->institutionMedicalProcedureTypes = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set institution
     *
     * @param HealthCareAbroad\InstitutionBundle\Entity\Institution $institution
     * @return InstitutionMedicalCenter
     */
    public function setInstitution(\HealthCareAbroad\InstitutionBundle\Entity\Institution $institution = null)
    {
        $this->institution = $institution;
        return $this;
    }

    /**
     * Get institution
     *
     * @return HealthCareAbroad\InstitutionBundle\Entity\Institution 
     */
    public function getInstitution()
    {
        return $this->institution;
    }
    
    /**
     * Add institutionMedicalProcedureTypes
     *
     * @param HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalProcedureType $institutionMedicalProcedureTypes
     * @return InstitutionMedicalCenter
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
}