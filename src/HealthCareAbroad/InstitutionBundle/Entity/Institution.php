<?php

namespace HealthCareAbroad\InstitutionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HealthCareAbroad\InstitutionBundle\Entity\Institution
 */
class Institution
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $description;

    /**
     * @var string
     */
    private $contactEmail;

    /**
     * @var string
     */
    private $contactNumber;

    /**
     * @var string
     */
    private $websites;

    /**
     * @var string
     */
    private $websiteBackUp;

    /**
     * @var string
     */
    private $socialMediaSites;

    /**
     * @var string
     */
    private $address1;

    /**
     * @var string
     */
    private $addressHint;

    /**
     * @var string
     */
    private $zipCode;

    /**
     * @var string
     */
    private $coordinates;

    /**
     * @var integer
     */
    private $payingClient;

    /**
     * @var integer
     */
    private $totalClinicRankingPoints;

    /**
     * @var integer
     */
    private $signupStepStatus;

    /**
     * @var \DateTime
     */
    private $dateModified;

    /**
     * @var \DateTime
     */
    private $dateCreated;

    /**
     * @var string
     */
    private $slug;

    /**
     * @var integer
     */
    private $isFromInternalAdmin;

    /**
     * @var integer
     */
    private $status;

    /**
     * @var integer
     */
    private $type;

    /**
     * @var string
     */
    private $stateBak;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $institutionMedicalCenters;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $institutionUsers;

    /**
     * @var \HealthCareAbroad\HelperBundle\Entity\Country
     */
    private $country;

    /**
     * @var \HealthCareAbroad\HelperBundle\Entity\City
     */
    private $city;

    /**
     * @var \HealthCareAbroad\HelperBundle\Entity\State
     */
    private $state;

    /**
     * @var \HealthCareAbroad\MediaBundle\Entity\Media
     */
    private $logo;

    /**
     * @var \HealthCareAbroad\MediaBundle\Entity\Media
     */
    private $featuredMedia;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $medicalProviderGroups;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $contactDetails;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->institutionMedicalCenters = new \Doctrine\Common\Collections\ArrayCollection();
        $this->institutionUsers = new \Doctrine\Common\Collections\ArrayCollection();
        $this->medicalProviderGroups = new \Doctrine\Common\Collections\ArrayCollection();
        $this->contactDetails = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @param string $description
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
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set contactEmail
     *
     * @param string $contactEmail
     * @return Institution
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
     * @return Institution
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
     * Set websites
     *
     * @param string $websites
     * @return Institution
     */
    public function setWebsites($websites)
    {
        $this->websites = $websites;
    
        return $this;
    }

    /**
     * Get websites
     *
     * @return string 
     */
    public function getWebsites()
    {
        return $this->websites;
    }

    /**
     * Set websiteBackUp
     *
     * @param string $websiteBackUp
     * @return Institution
     */
    public function setWebsiteBackUp($websiteBackUp)
    {
        $this->websiteBackUp = $websiteBackUp;
    
        return $this;
    }

    /**
     * Get websiteBackUp
     *
     * @return string 
     */
    public function getWebsiteBackUp()
    {
        return $this->websiteBackUp;
    }

    /**
     * Set socialMediaSites
     *
     * @param string $socialMediaSites
     * @return Institution
     */
    public function setSocialMediaSites($socialMediaSites)
    {
        $this->socialMediaSites = $socialMediaSites;
    
        return $this;
    }

    /**
     * Get socialMediaSites
     *
     * @return string 
     */
    public function getSocialMediaSites()
    {
        return $this->socialMediaSites;
    }

    /**
     * Set address1
     *
     * @param string $address1
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
     * @return string 
     */
    public function getAddress1()
    {
        return $this->address1;
    }

    /**
     * Set addressHint
     *
     * @param string $addressHint
     * @return Institution
     */
    public function setAddressHint($addressHint)
    {
        $this->addressHint = $addressHint;
    
        return $this;
    }

    /**
     * Get addressHint
     *
     * @return string 
     */
    public function getAddressHint()
    {
        return $this->addressHint;
    }

    /**
     * Set zipCode
     *
     * @param string $zipCode
     * @return Institution
     */
    public function setZipCode($zipCode)
    {
        $this->zipCode = $zipCode;
    
        return $this;
    }

    /**
     * Get zipCode
     *
     * @return string 
     */
    public function getZipCode()
    {
        return $this->zipCode;
    }

    /**
     * Set coordinates
     *
     * @param string $coordinates
     * @return Institution
     */
    public function setCoordinates($coordinates)
    {
        $this->coordinates = $coordinates;
    
        return $this;
    }

    /**
     * Get coordinates
     *
     * @return string 
     */
    public function getCoordinates()
    {
        return $this->coordinates;
    }

    /**
     * Set payingClient
     *
     * @param integer $payingClient
     * @return Institution
     */
    public function setPayingClient($payingClient)
    {
        $this->payingClient = $payingClient;
    
        return $this;
    }

    /**
     * Get payingClient
     *
     * @return integer 
     */
    public function getPayingClient()
    {
        return $this->payingClient;
    }

    /**
     * Set totalClinicRankingPoints
     *
     * @param integer $totalClinicRankingPoints
     * @return Institution
     */
    public function setTotalClinicRankingPoints($totalClinicRankingPoints)
    {
        $this->totalClinicRankingPoints = $totalClinicRankingPoints;
    
        return $this;
    }

    /**
     * Get totalClinicRankingPoints
     *
     * @return integer 
     */
    public function getTotalClinicRankingPoints()
    {
        return $this->totalClinicRankingPoints;
    }

    /**
     * Set signupStepStatus
     *
     * @param integer $signupStepStatus
     * @return Institution
     */
    public function setSignupStepStatus($signupStepStatus)
    {
        $this->signupStepStatus = $signupStepStatus;
    
        return $this;
    }

    /**
     * Get signupStepStatus
     *
     * @return integer 
     */
    public function getSignupStepStatus()
    {
        return $this->signupStepStatus;
    }

    /**
     * Set dateModified
     *
     * @param \DateTime $dateModified
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
     * @return \DateTime 
     */
    public function getDateModified()
    {
        return $this->dateModified;
    }

    /**
     * Set dateCreated
     *
     * @param \DateTime $dateCreated
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
     * @return \DateTime 
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
     * Set isFromInternalAdmin
     *
     * @param integer $isFromInternalAdmin
     * @return Institution
     */
    public function setIsFromInternalAdmin($isFromInternalAdmin)
    {
        $this->isFromInternalAdmin = $isFromInternalAdmin;
    
        return $this;
    }

    /**
     * Get isFromInternalAdmin
     *
     * @return integer 
     */
    public function getIsFromInternalAdmin()
    {
        return $this->isFromInternalAdmin;
    }

    /**
     * Set status
     *
     * @param integer $status
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
     * @return integer 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set type
     *
     * @param integer $type
     * @return Institution
     */
    public function setType($type)
    {
        $this->type = $type;
    
        return $this;
    }

    /**
     * Get type
     *
     * @return integer 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set stateBak
     *
     * @param string $stateBak
     * @return Institution
     */
    public function setStateBak($stateBak)
    {
        $this->stateBak = $stateBak;
    
        return $this;
    }

    /**
     * Get stateBak
     *
     * @return string 
     */
    public function getStateBak()
    {
        return $this->stateBak;
    }

    /**
     * Add institutionMedicalCenters
     *
     * @param \HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter $institutionMedicalCenters
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
     * @param \HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter $institutionMedicalCenters
     */
    public function removeInstitutionMedicalCenter(\HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter $institutionMedicalCenters)
    {
        $this->institutionMedicalCenters->removeElement($institutionMedicalCenters);
    }

    /**
     * Get institutionMedicalCenters
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getInstitutionMedicalCenters()
    {
        return $this->institutionMedicalCenters;
    }

    /**
     * Add institutionUsers
     *
     * @param \HealthCareAbroad\UserBundle\Entity\InstitutionUser $institutionUsers
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
     * @param \HealthCareAbroad\UserBundle\Entity\InstitutionUser $institutionUsers
     */
    public function removeInstitutionUser(\HealthCareAbroad\UserBundle\Entity\InstitutionUser $institutionUsers)
    {
        $this->institutionUsers->removeElement($institutionUsers);
    }

    /**
     * Get institutionUsers
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getInstitutionUsers()
    {
        return $this->institutionUsers;
    }

    /**
     * Set country
     *
     * @param \HealthCareAbroad\HelperBundle\Entity\Country $country
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
     * @return \HealthCareAbroad\HelperBundle\Entity\Country 
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set city
     *
     * @param \HealthCareAbroad\HelperBundle\Entity\City $city
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
     * @return \HealthCareAbroad\HelperBundle\Entity\City 
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set state
     *
     * @param \HealthCareAbroad\HelperBundle\Entity\State $state
     * @return Institution
     */
    public function setState(\HealthCareAbroad\HelperBundle\Entity\State $state = null)
    {
        $this->state = $state;
    
        return $this;
    }

    /**
     * Get state
     *
     * @return \HealthCareAbroad\HelperBundle\Entity\State 
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set logo
     *
     * @param \HealthCareAbroad\MediaBundle\Entity\Media $logo
     * @return Institution
     */
    public function setLogo(\HealthCareAbroad\MediaBundle\Entity\Media $logo = null)
    {
        $this->logo = $logo;
    
        return $this;
    }

    /**
     * Get logo
     *
     * @return \HealthCareAbroad\MediaBundle\Entity\Media 
     */
    public function getLogo()
    {
        return $this->logo;
    }

    /**
     * Set featuredMedia
     *
     * @param \HealthCareAbroad\MediaBundle\Entity\Media $featuredMedia
     * @return Institution
     */
    public function setFeaturedMedia(\HealthCareAbroad\MediaBundle\Entity\Media $featuredMedia = null)
    {
        $this->featuredMedia = $featuredMedia;
    
        return $this;
    }

    /**
     * Get featuredMedia
     *
     * @return \HealthCareAbroad\MediaBundle\Entity\Media 
     */
    public function getFeaturedMedia()
    {
        return $this->featuredMedia;
    }

    /**
     * Add medicalProviderGroups
     *
     * @param \HealthCareAbroad\InstitutionBundle\Entity\MedicalProviderGroup $medicalProviderGroups
     * @return Institution
     */
    public function addMedicalProviderGroup(\HealthCareAbroad\InstitutionBundle\Entity\MedicalProviderGroup $medicalProviderGroups)
    {
        $this->medicalProviderGroups[] = $medicalProviderGroups;
    
        return $this;
    }

    /**
     * Remove medicalProviderGroups
     *
     * @param \HealthCareAbroad\InstitutionBundle\Entity\MedicalProviderGroup $medicalProviderGroups
     */
    public function removeMedicalProviderGroup(\HealthCareAbroad\InstitutionBundle\Entity\MedicalProviderGroup $medicalProviderGroups)
    {
        $this->medicalProviderGroups->removeElement($medicalProviderGroups);
    }

    /**
     * Get medicalProviderGroups
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMedicalProviderGroups()
    {
        return $this->medicalProviderGroups;
    }

    /**
     * Add contactDetails
     *
     * @param \HealthCareAbroad\HelperBundle\Entity\ContactDetail $contactDetails
     * @return Institution
     */
    public function addContactDetail(\HealthCareAbroad\HelperBundle\Entity\ContactDetail $contactDetails)
    {
        $this->contactDetails[] = $contactDetails;
    
        return $this;
    }

    /**
     * Remove contactDetails
     *
     * @param \HealthCareAbroad\HelperBundle\Entity\ContactDetail $contactDetails
     */
    public function removeContactDetail(\HealthCareAbroad\HelperBundle\Entity\ContactDetail $contactDetails)
    {
        $this->contactDetails->removeElement($contactDetails);
    }

    /**
     * Get contactDetails
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getContactDetails()
    {
        return $this->contactDetails;
    }
}