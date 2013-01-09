<?php
namespace HealthCareAbroad\DoctorBundle\Entity;
class Doctor
{
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

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
     * @var string $contactEmail
     */
    private $contactEmail;

    /**
     * @var string $contactNumber
     */
    private $contactNumber;
    
    /**
     * @var string $details
     */
    private $details;

    /**
     * @var datetime $dateCreated
     */
    private $dateCreated;

    /**
     * @var boolean $status
     */
    private $status;
    
    /**
     * @var HealthCareAbroad\HelperBundle\Entity\Country
     */
    private $country;

    /**
     * @var HealthCareAbroad\MediaBundle\Entity\Media
     */
    private $media;

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
     * Set contactEmail
     *
     * @param string $contactEmail
     * @return Doctor
     */
    public function setContactEmail($contactEmail)
    {
        $this->contactEmail = $contactEmail;
        return $this;
    }

    /**
     * Get contactEmail
     *
     * @return string 
     */
    public function getContactEmail()
    {
        return $this->contactEmail;
    }

    /**
     * Set contactNumber
     *
     * @param string $contactNumber
     * @return Doctor
     */
    public function setContactNumber($contactNumber)
    {
        $this->contactNumber = $contactNumber;
        return $this;
    }

    /**
     * Get contactNumber
     *
     * @return string 
     */
    public function getContactNumber()
    {
        return $this->contactNumber;
    }
    
    /**
     * Set details
     *
     * @param string $details
     * @return Doctor
     */
    public function setDetails($details)
    {
        $this->details = $details;
        return $this;
    }
    
    /**
     * Get details
     *
     * @return string
     */
    public function getDetails()
    {
        return $this->details;
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
     * Set media
     *
     * @param HealthCareAbroad\MediaBundle\Entity\Media $media
     * @return Doctor
     */
    public function setMedia(\HealthCareAbroad\MediaBundle\Entity\Media $media = null)
    {
        $this->media = $media;
        return $this;
    }

    /**
     * Get media
     *
     * @return HealthCareAbroad\MediaBundle\Entity\Media 
     */
    public function getMedia()
    {
        return $this->media;
    }

    /**
     * Add specializations
     *
     * @param HealthCareAbroad\TreatmentBundle\Entity\Specialization $specializations
     * @return Doctor
     */
    public function addSpecialization(\HealthCareAbroad\TreatmentBundle\Entity\Specialization $specializations)
    {
        $this->specializations[] = $specializations;
        return $this;
    }

    /**
     * Remove specializations
     *
     * @param HealthCareAbroad\TreatmentBundle\Entity\Specialization $specializations
     */
    public function removeSpecialization(\HealthCareAbroad\TreatmentBundle\Entity\Specialization $specializations)
    {
        $this->specializations->removeElement($specializations);
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
     * Add institutionMedicalCenters
     *
     * @param HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter $institutionMedicalCenters
     * @return Doctor
     */
    public function addInstitutionMedicalCenter(\HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter $institutionMedicalCenters)
    {
        $this->institutionMedicalCenters[] = $institutionMedicalCenters;
        return $this;
    }

    /**
     * Remove institutionMedicalCenters
     *
     * @param HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter $institutionMedicalCenters
     */
    public function removeInstitutionMedicalCenter(\HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter $institutionMedicalCenters)
    {
        $this->institutionMedicalCenters->removeElement($institutionMedicalCenters);
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
    
    /**
     * Set country
     *
     * @param HealthCareAbroad\HelperBundle\Entity\Country $country
     * @return Institution
     */
    public function setCountry(\HealthCareAbroad\HelperBundle\Entity\Country $country = null)
    {
        $this->country = $country;
        return $this;
    }
    
    /**
     * Get country
     *
     * @return HealthCareAbroad\HelperBundle\Entity\Country
     */
    public function getCountry()
    {
        return $this->country;
    }
}