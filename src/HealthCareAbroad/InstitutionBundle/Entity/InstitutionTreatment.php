<?php
namespace HealthCareAbroad\InstitutionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

class InstitutionTreatment
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
    private $institutionTreatmentProcedures;

    /**
     * @var HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter
     */
    private $institutionMedicalCenter;

    /**
     * @var HealthCareAbroad\MedicalProcedureBundle\Entity\Treatment
     */
    private $treatment;

    public function __construct()
    {
        $this->institutionTreatmentProcedures = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return InstitutionTreatment
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
     * @return InstitutionTreatment
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
     * @return InstitutionTreatment
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
     * @return InstitutionTreatment
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
     * Add institutionTreatmentProcedures
     *
     * @param HealthCareAbroad\InstitutionBundle\Entity\InstitutionTreatmentProcedure $institutionTreatmentProcedures
     * @return InstitutionTreatment
     */
    public function addInstitutionTreatmentProcedure(\HealthCareAbroad\InstitutionBundle\Entity\InstitutionTreatmentProcedure $institutionTreatmentProcedures)
    {
        $this->institutionTreatmentProcedures[] = $institutionTreatmentProcedures;
        return $this;
    }

    /**
     * Remove institutionTreatmentProcedures
     *
     * @param HealthCareAbroad\InstitutionBundle\Entity\InstitutionTreatmentProcedure $institutionTreatmentProcedures
     */
    public function removeInstitutionTreatmentProcedure(\HealthCareAbroad\InstitutionBundle\Entity\InstitutionTreatmentProcedure $institutionTreatmentProcedures)
    {
        $this->institutionTreatmentProcedures->removeElement($institutionTreatmentProcedures);
    }

    /**
     * Get institutionTreatmentProcedures
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getInstitutionTreatmentProcedures()
    {
        return $this->institutionTreatmentProcedures;
    }

    /**
     * Set institutionMedicalCenter
     *
     * @param HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter $institutionMedicalCenter
     * @return InstitutionTreatment
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
     * Set treatment
     *
     * @param HealthCareAbroad\MedicalProcedureBundle\Entity\Treatment $treatment
     * @return InstitutionTreatment
     */
    public function setTreatment(\HealthCareAbroad\MedicalProcedureBundle\Entity\Treatment $treatment = null)
    {
        $this->treatment = $treatment;
        return $this;
    }

    /**
     * Get treatment
     *
     * @return HealthCareAbroad\MedicalProcedureBundle\Entity\Treatment 
     */
    public function getTreatment()
    {
        return $this->treatment;
    }
}