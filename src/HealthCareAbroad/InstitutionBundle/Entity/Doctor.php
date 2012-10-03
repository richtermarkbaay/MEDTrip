<?php

namespace HealthCareAbroad\InstitutionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HealthCareAbroad\InstitutionBundle\Entity\Doctor
 */
class Doctor
{
    /**
     * Doctors that are active
     */
    const STATUS_ACTIVE = 1;
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
    private $institutionDoctors;

    public function __construct()
    {
        $this->institutionDoctors = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Add institutionDoctors
     *
     * @param HealthCareAbroad\InstitutionBundle\Entity\InstitutionDoctor $institutionDoctors
     * @return Doctor
     */
    public function addInstitutionDoctor(\HealthCareAbroad\InstitutionBundle\Entity\InstitutionDoctor $institutionDoctors)
    {
        $this->institutionDoctors[] = $institutionDoctors;
        return $this;
    }

    /**
     * Remove institutionDoctors
     *
     * @param HealthCareAbroad\InstitutionBundle\Entity\InstitutionDoctor $institutionDoctors
     */
    public function removeInstitutionDoctor(\HealthCareAbroad\InstitutionBundle\Entity\InstitutionDoctor $institutionDoctors)
    {
        $this->institutionDoctors->removeElement($institutionDoctors);
    }

    /**
     * Get institutionDoctors
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getInstitutionDoctors()
    {
        return $this->institutionDoctors;
    }
}