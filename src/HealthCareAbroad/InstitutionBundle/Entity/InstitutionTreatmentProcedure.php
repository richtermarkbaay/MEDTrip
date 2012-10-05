<?php

namespace HealthCareAbroad\InstitutionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

class InstitutionTreatmentProcedure
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
     * @var HealthCareAbroad\MedicalProcedureBundle\Entity\TreatmentProcedure
     */
    private $treatmentProcedure;

    /**
     * @var HealthCareAbroad\InstitutionBundle\Entity\InstitutionTreatment
     */
    private $institutionTreatment;


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
     * @return InstitutionTreatmentProcedure
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
     * @return InstitutionTreatmentProcedure
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
     * @return InstitutionTreatmentProcedure
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
     * @return InstitutionTreatmentProcedure
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
     * Set treatmentProcedure
     *
     * @param HealthCareAbroad\MedicalProcedureBundle\Entity\TreatmentProcedure $treatmentProcedure
     * @return InstitutionTreatmentProcedure
     */
    public function setTreatmentProcedure(\HealthCareAbroad\MedicalProcedureBundle\Entity\TreatmentProcedure $treatmentProcedure = null)
    {
        $this->treatmentProcedure = $treatmentProcedure;
        return $this;
    }

    /**
     * Get treatmentProcedure
     *
     * @return HealthCareAbroad\MedicalProcedureBundle\Entity\TreatmentProcedure 
     */
    public function getTreatmentProcedure()
    {
        return $this->treatmentProcedure;
    }

    /**
     * Set institutionTreatment
     *
     * @param HealthCareAbroad\InstitutionBundle\Entity\InstitutionTreatment $institutionTreatment
     * @return InstitutionTreatmentProcedure
     */
    public function setInstitutionTreatment(\HealthCareAbroad\InstitutionBundle\Entity\InstitutionTreatment $institutionTreatment = null)
    {
        $this->institutionTreatment = $institutionTreatment;
        return $this;
    }

    /**
     * Get institutionTreatment
     *
     * @return HealthCareAbroad\InstitutionBundle\Entity\InstitutionTreatment 
     */
    public function getInstitutionTreatment()
    {
        return $this->institutionTreatment;
    }
    
    public function __toString()
    {
        return $this->treatmentProcedure ? $this->treatmentProcedure->getName() : null;
    }
}