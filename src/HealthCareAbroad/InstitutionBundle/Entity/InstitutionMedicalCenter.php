<?php

namespace HealthCareAbroad\InstitutionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter
 */
class InstitutionMedicalCenter
{
    /**
     * @var integer $id
     */
    private $id;

    /**
     * @var HealthCareAbroad\MedicalProcedureBundle\Entity\MedicalCenter
     */
    private $medicalCenter;

    /**
     * @var HealthCareAbroad\InstitutionBundle\Entity\Institution
     */
    private $institution;


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
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $medicalProcedureTypes;

    public function __construct()
    {
        $this->medicalProcedureTypes = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Add medicalProcedureTypes
     *
     * @param HealthCareAbroad\MedicalProcedureBundle\Entity\MedicalProcedureType $medicalProcedureTypes
     * @return InstitutionMedicalCenter
     */
    public function addMedicalProcedureType(\HealthCareAbroad\MedicalProcedureBundle\Entity\MedicalProcedureType $medicalProcedureTypes)
    {
        $this->medicalProcedureTypes[] = $medicalProcedureTypes;
        return $this;
    }

    /**
     * Remove medicalProcedureTypes
     *
     * @param HealthCareAbroad\MedicalProcedureBundle\Entity\MedicalProcedureType $medicalProcedureTypes
     */
    public function removeMedicalProcedureType(\HealthCareAbroad\MedicalProcedureBundle\Entity\MedicalProcedureType $medicalProcedureTypes)
    {
        $this->medicalProcedureTypes->removeElement($medicalProcedureTypes);
    }

    /**
     * Get medicalProcedureTypes
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getMedicalProcedureTypes()
    {
        return $this->medicalProcedureTypes;
    }
}