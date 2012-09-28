<?php

namespace HealthCareAbroad\InstitutionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HealthCareAbroad\InstitutionBundle\Entity\Institution
 */
class Institution
{
    const ACTIVE = 1;
    
    const INACTIVE = 2;
    
    const UNAPPROVED = 4;
    
    const APPROVED = 8;
    
    const SUSPENDED = 16;
    
    const USER_TYPE = "SUPER_ADMIN";
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
     * @var string $logo
     */
    private $logo;

    /**
     * @var text $address1
     */
    private $address1;

    /**
     * @var text $address2
     */
    private $address2;

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
    private $institutionMedicalCenters;

    /**
     * @var HealthCareAbroad\HelperBundle\Entity\Country
     */
    private $country;

    /**
     * @var HealthCareAbroad\HelperBundle\Entity\City
     */
    private $city;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $contactDetail;
    
    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $institutionMedicalProcedureTypes;
    
    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $institutionUsers;
    
    
    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    public $institutionOfferedServices;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    public $institutionLanguagesSpoken;
    
    public function __construct()
    {
        $this->institutionMedicalCenters = new \Doctrine\Common\Collections\ArrayCollection();
        $this->contactDetail = new \Doctrine\Common\Collections\ArrayCollection();
    }

    
    //---- status operations
    public function setAsActive()
    {
        $this->status = self::ACTIVE;
    }
    
    public function setAsInactive()
    {
        $this->status = self::INACTIVE;
    }
    
    public function setAsUnapproved()
    {
        $this->status = self::ACTIVE + self::UNAPPROVED;   
    }
    
    public function setAsApproved()
    {
        $this->status = self::ACTIVE + self::APPROVED;
    }
    
    public function setAsSuspended()
    {
        $this->status = self::ACTIVE + self::SUSPENDED;
    }
    
    /**
     * Check if this is institution is active
     * 
     * @return boolean
     */
    public function isActive()
    {
        return self::ACTIVE == ($this->status & self::ACTIVE);
    }
    
    /**
     * Check if the institution is inactive
     * 
     * @return boolean
     */
    public function isInactive()
    {
        return $this->status == self::INACTIVE;
    }
    
    /**
     * Check if the institution is unapproved
     *
     * @return boolean
     */
    public function isUnapproved()
    {
        return $this->status == self::ACTIVE + self::UNAPPROVED;
    }
    
    /**
     * Check if the institution is approved
     *
     * @return boolean
     */
    public function isApproved()
    {
        return $this->status == self::ACTIVE + self::APPROVED;
    }
    
    /**
     * Check if the institution is suspended
     *
     * @return boolean
     */
    public function isSuspended()
    {
        return $this->status == self::ACTIVE + self::SUSPENDED;
    }
    //---- end status operations
    
    
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
     * @return Institution
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
     * @return Institution
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
     * Set logo
     *
     * @param string $logo
     * @return Institution
     */
    public function setLogo($logo)
    {
        $this->logo = $logo;
        return $this;
    }

    /**
     * Get logo
     *
     * @return string 
     */
    public function getLogo()
    {
        return $this->logo;
    }

    /**
     * Set address1
     *
     * @param text $address1
     * @return Institution
     */
    public function setAddress1($address1)
    {
        $this->address1 = $address1;
        return $this;
    }

    /**
     * Get address1
     *
     * @return text 
     */
    public function getAddress1()
    {
        return $this->address1;
    }

    /**
     * Set address2
     *
     * @param text $address2
     * @return Institution
     */
    public function setAddress2($address2)
    {
        $this->address2 = $address2;
        return $this;
    }

    /**
     * Get address2
     *
     * @return text 
     */
    public function getAddress2()
    {
        return $this->address2;
    }

    /**
     * Set dateModified
     *
     * @param datetime $dateModified
     * @return Institution
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
     * @return Institution
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
     * @return Institution
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
     * @return Institution
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
     * Add institutionMedicalCenters
     *
     * @param HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter $institutionMedicalCenters
     * @return Institution
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

    /**
     * Set city
     *
     * @param HealthCareAbroad\HelperBundle\Entity\City $city
     * @return Institution
     */
    public function setCity(\HealthCareAbroad\HelperBundle\Entity\City $city = null)
    {
        $this->city = $city;
        return $this;
    }

    /**
     * Get city
     *
     * @return HealthCareAbroad\HelperBundle\Entity\City 
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Add contactDetail
     *
     * @param HealthCareAbroad\HelperBundle\Entity\ContactDetail $contactDetail
     * @return Institution
     */
    public function addContactDetail(\HealthCareAbroad\HelperBundle\Entity\ContactDetail $contactDetail)
    {
        $this->contactDetail[] = $contactDetail;
        return $this;
    }

    /**
     * Remove contactDetail
     *
     * @param HealthCareAbroad\HelperBundle\Entity\ContactDetail $contactDetail
     */
    public function removeContactDetail(\HealthCareAbroad\HelperBundle\Entity\ContactDetail $contactDetail)
    {
        $this->contactDetail->removeElement($contactDetail);
    }

    /**
     * Get contactDetail
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getContactDetail()
    {
        return $this->contactDetail;
    }


    /**
     * Add institutionMedicalProcedureTypes
     *
     * @param HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalProcedureType $institutionMedicalProcedureTypes
     * @return Institution
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


    /**
     * Add institutionUsers
     *
     * @param HealthCareAbroad\UserBundle\Entity\InstitutionUser $institutionUsers
     * @return Institution
     */
    public function addInstitutionUser(\HealthCareAbroad\UserBundle\Entity\InstitutionUser $institutionUsers)
    {
        $this->institutionUsers[] = $institutionUsers;
        return $this;
    }

    /**
     * Remove institutionUsers
     *
     * @param HealthCareAbroad\UserBundle\Entity\InstitutionUser $institutionUsers
     */
    public function removeInstitutionUser(\HealthCareAbroad\UserBundle\Entity\InstitutionUser $institutionUsers)
    {
        $this->institutionUsers->removeElement($institutionUsers);
    }

    /**
     * Get institutionUsers
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getInstitutionUsers()
    {
        return $this->institutionUsers;
    }

    /**
     * Add institutionOfferedServices
     *
     * @param HealthCareAbroad\AdminBundle\Entity\OfferedService $institutionOfferedServices
     * @return Institution
     */
    public function addInstitutionOfferedService(\HealthCareAbroad\AdminBundle\Entity\OfferedService $institutionOfferedServices)
    {
        $this->institutionOfferedServices[] = $institutionOfferedServices;
        return $this;
    }

    /**
     * Remove institutionOfferedServices
     *
     * @param HealthCareAbroad\AdminBundle\Entity\OfferedService $institutionOfferedServices
     */
    public function removeInstitutionOfferedService(\HealthCareAbroad\AdminBundle\Entity\OfferedService $institutionOfferedServices)
    {
        $this->institutionOfferedServices->removeElement($institutionOfferedServices);
    }

    /**
     * Get institutionOfferedServices
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getInstitutionOfferedServices()
    {
        return $this->institutionOfferedServices;
    }
    
    /**
     * Add institution Language Spoken
     *
     * @param HealthCareAbroad\AdminBundle\Entity\Language $institutionLanguagesSpoken
     * @return Institution
     */
    public function addInstitutionLanguagesSpoken(\HealthCareAbroad\AdminBundle\Entity\Language $institutionLanguagesSpoken)
    {
    	$this->institutionLanguagesSpoken[] = $institutionLanguagesSpoken;
    	return $this;
    }
    
    /**
     * Remove institution Language Spoken
     *
     * @param HealthCareAbroad\AdminBundle\Entity\Language $institutionLanguagesSpoken
     */
    public function removeInstitutionLanguagesSpoken(\HealthCareAbroad\AdminBundle\Entity\Language $institutionLanguagesSpoken)
    {
    	$this->institutionLanguagesSpoken->removeElement($institutionLanguagesSpoken);
    }
    
    /**
     * Get institution Language SPoken
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getInstitutionLanguagesSpoken()
    {
    	return $this->institutionLanguagesSpoken;
    }
}