<?php

namespace HealthCareAbroad\MedicalProcedureBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

class TreatmentProcedure
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
    private $institutionTreatmentProcedures;

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
     * Set name
     *
     * @param string $name
     * @return TreatmentProcedure
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
     * @return TreatmentProcedure
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
     * @return TreatmentProcedure
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
     * @return TreatmentProcedure
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
     * Set treatment
     *
     * @param HealthCareAbroad\MedicalProcedureBundle\Entity\Treatment $treatment
     * @return TreatmentProcedure
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
    
    public function __toString()
    {
        return $this->name;
    }
}