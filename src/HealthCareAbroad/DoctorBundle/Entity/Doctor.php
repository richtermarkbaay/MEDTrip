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
    private $specializations;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $institutionMedicalCenters;

    public function __construct()
    {
        $this->specializations = new \Doctrine\Common\Collections\ArrayCollection();
        $this->institutionMedicalCenters = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Add specialization
     *
     * @param HealthCareAbroad\TreatmentBundle\Entity\Specialization $specialization
     * @return Doctor
     */
    public function addSpecialization(\HealthCareAbroad\TreatmentBundle\Entity\Specialization $specialization)
    {
        $this->specializations[] = $specialization;
        return $this;
    }

    /**
     * Remove specialization
     *
     * @param HealthCareAbroad\TreatmentBundle\Entity\Specialization $specialization
     */
    public function removeSpecialization(\HealthCareAbroad\TreatmentBundle\Entity\Specialization $specialization)
    {
        $this->specializations->removeElement($specialization);
    }

    /**
     * Get specializations
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getSpecializations()
    {
        return $this->specializations;
    }

    /**
     * Add institutionMedicalCenter
     *
     * @param HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter $institutionMedicalCenter
     * @return Doctor
     */
    public function addInstitutionMedicalCenter(\HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter $institutionMedicalCenter)
    {
        $this->institutionMedicalCenters[] = $institutionMedicalCenter;
        return $this;
    }

    /**
     * Remove institutionMedicalCenter
     *
     * @param HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter $institutionMedicalCenter
     */
    public function removeInstitutionMedicalCenterGroup(\HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter $institutionMedicalCenter)
    {
        $this->institutionMedicalCenters->removeElement($institutionMedicalCenter);
    }

    /**
     * Get institutionMedicalCenters
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getInstitutionMedicalCenters()
    {
        return $this->institutionMedicalCenters;
    }
}