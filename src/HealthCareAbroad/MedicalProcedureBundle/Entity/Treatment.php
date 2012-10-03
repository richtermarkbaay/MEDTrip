<?php

namespace HealthCareAbroad\MedicalProcedureBundle\Entity;

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
    private $treatmentProcedures;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $institutionTreatments;

    /**
     * @var HealthCareAbroad\MedicalProcedureBundle\Entity\MedicalCenter
     */
    private $medicalCenter;

    public function __construct()
    {
        $this->treatmentProcedures = new \Doctrine\Common\Collections\ArrayCollection();
        $this->institutionTreatments = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set description
     *
     * @param text $description
     * @return Treatment
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
     * @return Treatment
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
     * @return Treatment
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
     * Add treatmentProcedures
     *
     * @param HealthCareAbroad\MedicalProcedureBundle\Entity\TreatmentProcedure $treatmentProcedures
     * @return Treatment
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

    /**
     * Add institutionTreatments
     *
     * @param HealthCareAbroad\InstitutionBundle\Entity\InstitutionTreatment $institutionTreatments
     * @return Treatment
     */
    public function addInstitutionTreatment(\HealthCareAbroad\InstitutionBundle\Entity\InstitutionTreatment $institutionTreatments)
    {
        $this->institutionTreatments[] = $institutionTreatments;
        return $this;
    }

    /**
     * Remove institutionTreatments
     *
     * @param HealthCareAbroad\InstitutionBundle\Entity\InstitutionTreatment $institutionTreatments
     */
    public function removeInstitutionTreatment(\HealthCareAbroad\InstitutionBundle\Entity\InstitutionTreatment $institutionTreatments)
    {
        $this->institutionTreatments->removeElement($institutionTreatments);
    }

    /**
     * Get institutionTreatments
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getInstitutionTreatments()
    {
        return $this->institutionTreatments;
    }

    /**
     * Set medicalCenter
     *
     * @param HealthCareAbroad\MedicalProcedureBundle\Entity\MedicalCenter $medicalCenter
     * @return Treatment
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