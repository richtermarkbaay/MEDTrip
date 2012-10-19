<?php
namespace HealthCareAbroad\DoctorBundle\Entity;
class Doctor
{
    /**
     * @var bigint $id
     */
    private $id;

    /**
     * @var string $firstName
     */
    private $firstName;

    /**
     * @var string $middleName
     */
    private $middleName;

    /**
     * @var string $lastName
     */
    private $lastName;

    /**
     * @var datetime $dateCreated
     */
    private $dateCreated;

    /**
     * @var boolean $status
     */
    private $status;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $medicalCenters;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $institutionMedicalCenterGroups;

    public function __construct()
    {
        $this->medicalCenters = new \Doctrine\Common\Collections\ArrayCollection();
        $this->institutionMedicalCenterGroups = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set firstName
     *
     * @param string $firstName
     * @return Doctor
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
        return $this;
    }

    /**
     * Get firstName
     *
     * @return string 
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set middleName
     *
     * @param string $middleName
     * @return Doctor
     */
    public function setMiddleName($middleName)
    {
        $this->middleName = $middleName;
        return $this;
    }

    /**
     * Get middleName
     *
     * @return string 
     */
    public function getMiddleName()
    {
        return $this->middleName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     * @return Doctor
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
        return $this;
    }

    /**
     * Get lastName
     *
     * @return string 
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set dateCreated
     *
     * @param datetime $dateCreated
     * @return Doctor
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
     * @param boolean $status
     * @return Doctor
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * Get status
     *
     * @return boolean 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Add medicalCenters
     *
     * @param HealthCareAbroad\MedicalProcedureBundle\Entity\MedicalCenter $medicalCenters
     * @return Doctor
     */
    public function addMedicalCenter(\HealthCareAbroad\MedicalProcedureBundle\Entity\MedicalCenter $medicalCenters)
    {
        $this->medicalCenters[] = $medicalCenters;
        return $this;
    }

    /**
     * Remove medicalCenters
     *
     * @param HealthCareAbroad\MedicalProcedureBundle\Entity\MedicalCenter $medicalCenters
     */
    public function removeMedicalCenter(\HealthCareAbroad\MedicalProcedureBundle\Entity\MedicalCenter $medicalCenters)
    {
        $this->medicalCenters->removeElement($medicalCenters);
    }

    /**
     * Get medicalCenters
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getMedicalCenters()
    {
        return $this->medicalCenters;
    }

    /**
     * Add institutionMedicalCenterGroups
     *
     * @param HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenterGroup $institutionMedicalCenterGroups
     * @return Doctor
     */
    public function addInstitutionMedicalCenterGroup(\HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenterGroup $institutionMedicalCenterGroups)
    {
        $this->institutionMedicalCenterGroups[] = $institutionMedicalCenterGroups;
        return $this;
    }

    /**
     * Remove institutionMedicalCenterGroups
     *
     * @param HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenterGroup $institutionMedicalCenterGroups
     */
    public function removeInstitutionMedicalCenterGroup(\HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenterGroup $institutionMedicalCenterGroups)
    {
        $this->institutionMedicalCenterGroups->removeElement($institutionMedicalCenterGroups);
    }

    /**
     * Get institutionMedicalCenterGroups
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getInstitutionMedicalCenterGroups()
    {
        return $this->institutionMedicalCenterGroups;
    }
}