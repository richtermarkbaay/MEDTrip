<?php

namespace HealthCareAbroad\InstitutionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HealthCareAbroad\InstitutionBundle\Entity\Institution
 */
class Institution
{
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
     * @var integer $logo
     */
    private $logo;

    /**
     * @var string $contactEmail
     */
    private $contactEmail;

    /**
     * @var string $contactNumber
     */
    private $contactNumber;

    /**
     * @var string $websites
     */
    private $websites;

    /**
     * @var text $address1
     */
    private $address1;

    /**
     * @var integer $zipCode
     */
    private $zipCode;

    /**
     * @var string $state
     */
    private $state;
    
    /**
     * @var string $coordinates
     */
    private $coordinates;

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
     * @var integer $type
     */
    private $type;

    /**
     * @var HealthCareAbroad\MediaBundle\Entity\Gallery
     */
    private $gallery;

    /**
     * @var HealthCareAbroad\MediaBundle\Entity\Media
     */
    private $media;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $institutionMedicalCenters;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $institutionUsers;

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
    private $institutionOfferedServices;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $institutionLanguagesSpoken;
    
    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $medicalProviderGroups;

    public function __construct()
    {
        $this->institutionMedicalCenters = new \Doctrine\Common\Collections\ArrayCollection();
        $this->institutionUsers = new \Doctrine\Common\Collections\ArrayCollection();
        $this->institutionOfferedServices = new \Doctrine\Common\Collections\ArrayCollection();
        $this->institutionLanguagesSpoken = new \Doctrine\Common\Collections\ArrayCollection();
        $this->medicalProviderGroups = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @param integer $logo
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
     * @return integer 
     */
    public function getLogo()
    {
        return $this->logo;
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
     * Set zipCode
     *
     * @param integer $zipCode
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
     * @return integer 
     */
    public function getZipCode()
    {
        return $this->zipCode;
    }

    /**
     * Set state
     *
     * @param string $state
     * @return Institution
     */
    public function setState($state)
    {
        $this->state = $state;
        return $this;
    }

    /**
     * Get state
     *
     * @return string 
     */
    public function getState()
    {
        return $this->state;
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
     * Set gallery
     *
     * @param HealthCareAbroad\MediaBundle\Entity\Gallery $gallery
     * @return Institution
     */
    public function setGallery(\HealthCareAbroad\MediaBundle\Entity\Gallery $gallery = null)
    {
        $this->gallery = $gallery;
        return $this;
    }

    /**
     * Get gallery
     *
     * @return HealthCareAbroad\MediaBundle\Entity\Gallery 
     */
    public function getGallery()
    {
        return $this->gallery;
    }

    /**
     * Set media
     *
     * @param HealthCareAbroad\MediaBundle\Entity\Media $media
     * @return Institution
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
     * Add institutionLanguagesSpoken
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
     * Remove institutionLanguagesSpoken
     *
     * @param HealthCareAbroad\AdminBundle\Entity\Language $institutionLanguagesSpoken
     */
    public function removeInstitutionLanguagesSpoken(\HealthCareAbroad\AdminBundle\Entity\Language $institutionLanguagesSpoken)
    {
        $this->institutionLanguagesSpoken->removeElement($institutionLanguagesSpoken);
    }

    /**
     * Get institutionLanguagesSpoken
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getInstitutionLanguagesSpoken()
    {
        return $this->institutionLanguagesSpoken;
    }
    
    /**
     * Add medicalProviderGroup
     *
     * @param HealthCareAbroad\InstitutionBundle\Entity\MedicalProviderGroup $medicalProviderGroups
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
     * @param HealthCareAbroad\InstitutionBundle\Entity\MedicalProviderGroup $medicalProviderGroups
     */
    public function removeMedicalProviderGroup(\HealthCareAbroad\InstitutionBundle\Entity\MedicalProviderGroup $medicalProviderGroups)
    {
        $this->medicalProviderGroups->removeElement($medicalProviderGroups);
    }
    
    /**
     * Get medicalProviderGroups
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getMedicalProviderGroups()
    {
        return $this->medicalProviderGroups;
    }
}