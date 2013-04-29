<?php
namespace HealthCareAbroad\InstitutionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter
 * @author Allejo Chris G. Velarde
 *
 */
class InstitutionMedicalCenter
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
    private $businessHours;

    /**
     * @var string
     */
    private $descriptionHighlight;

    /**
     * @var string
     */
    private $description;

    /**
     * @var string
     */
    private $address;
    
    /**
     * @var text $addressHint
     */
    private $addressHint;
    
    /**
     * @var string
     */
    private $coordinates;

    /**
     * @var string
     */
    private $contactNumber;

    /**
     * @var string
     */
    private $contactEmail;

    /**
     * @var string
     */
    private $websites;

    /**
     * @var string
     */
    private $socialMediaSites;

    /**
     * @var \DateTime
     */
    private $dateCreated;

    /**
     * @var \DateTime
     */
    private $dateUpdated;

    /**
     * @var integer
     */
    private $status;

    /**
     * @var string
     */
    private $slug;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $institutionSpecializations;

    /**
     * @var \HealthCareAbroad\MediaBundle\Entity\Media
     */
    private $logo;

    /**
     * @var \HealthCareAbroad\InstitutionBundle\Entity\Institution
     */
    private $institution;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $media;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $doctors;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $contactDetails;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->institutionSpecializations = new \Doctrine\Common\Collections\ArrayCollection();
        $this->media = new \Doctrine\Common\Collections\ArrayCollection();
        $this->doctors = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return InstitutionMedicalCenter
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
     * Set businessHours
     *
     * @param string $businessHours
     * @return InstitutionMedicalCenter
     */
    public function setBusinessHours($businessHours)
    {
        $this->businessHours = $businessHours;
    
        return $this;
    }

    /**
     * Get businessHours
     *
     * @return string 
     */
    public function getBusinessHours()
    {
        return $this->businessHours;
    }

    /**
     * Set descriptionHighlight
     *
     * @param string $descriptionHighlight
     * @return InstitutionMedicalCenter
     */
    public function setDescriptionHighlight($descriptionHighlight)
    {
        $this->descriptionHighlight = $descriptionHighlight;
    
        return $this;
    }

    /**
     * Get descriptionHighlight
     *
     * @return string 
     */
    public function getDescriptionHighlight()
    {
        return $this->descriptionHighlight;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return InstitutionMedicalCenter
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
     * Set address
     *
     * @param string $address
     * @return InstitutionMedicalCenter
     */
    public function setAddress($address)
    {
        $this->address = $address;
    
        return $this;
    }

    /**
     * Get address
     *
     * @return string 
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
    * Set addressHint
    *
    * @param text $addressHint
    * @return InstitutionMedicalCenter
    */
    public function setAddressHint($addressHint)
    {
        $this->addressHint = $addressHint;
        return $this;
    }
        
    /**
     * Get addressHint
     *
     * @return text
     */
    public function getAddressHInt()
    {
        return $this->addressHint;
    }
    
    /**
     * Set coordinates
     *
     * @param string $coordinates
     * @return InstitutionMedicalCenter
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
     * Set contactNumber
     *
     * @param string $contactNumber
     * @return InstitutionMedicalCenter
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
     * Set contactEmail
     *
     * @param string $contactEmail
     * @return InstitutionMedicalCenter
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
     * Set websites
     *
     * @param string $websites
     * @return InstitutionMedicalCenter
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
     * Set socialMediaSites
     *
     * @param string $socialMediaSites
     * @return InstitutionMedicalCenter
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
     * Set dateCreated
     *
     * @param \DateTime $dateCreated
     * @return InstitutionMedicalCenter
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
     * Set dateUpdated
     *
     * @param \DateTime $dateUpdated
     * @return InstitutionMedicalCenter
     */
    public function setDateUpdated($dateUpdated)
    {
        $this->dateUpdated = $dateUpdated;
    
        return $this;
    }

    /**
     * Get dateUpdated
     *
     * @return \DateTime 
     */
    public function getDateUpdated()
    {
        return $this->dateUpdated;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return InstitutionMedicalCenter
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
     * Set slug
     *
     * @param string $slug
     * @return InstitutionMedicalCenter
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
     * Add institutionSpecializations
     *
     * @param \HealthCareAbroad\InstitutionBundle\Entity\InstitutionSpecialization $institutionSpecializations
     * @return InstitutionMedicalCenter
     */
    public function addInstitutionSpecialization(\HealthCareAbroad\InstitutionBundle\Entity\InstitutionSpecialization $institutionSpecializations)
    {
        $this->institutionSpecializations[] = $institutionSpecializations;
    
        return $this;
    }

    /**
     * Remove institutionSpecializations
     *
     * @param \HealthCareAbroad\InstitutionBundle\Entity\InstitutionSpecialization $institutionSpecializations
     */
    public function removeInstitutionSpecialization(\HealthCareAbroad\InstitutionBundle\Entity\InstitutionSpecialization $institutionSpecializations)
    {
        $this->institutionSpecializations->removeElement($institutionSpecializations);
    }

    /**
     * Get institutionSpecializations
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getInstitutionSpecializations()
    {
        return $this->institutionSpecializations;
    }

    /**
     * Set logo
     *
     * @param \HealthCareAbroad\MediaBundle\Entity\Media $logo
     * @return InstitutionMedicalCenter
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
     * Set institution
     *
     * @param \HealthCareAbroad\InstitutionBundle\Entity\Institution $institution
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
     * @return \HealthCareAbroad\InstitutionBundle\Entity\Institution 
     */
    public function getInstitution()
    {
        return $this->institution;
    }

    /**
     * Add media
     *
     * @param \HealthCareAbroad\MediaBundle\Entity\Media $media
     * @return InstitutionMedicalCenter
     */
    public function addMedia(\HealthCareAbroad\MediaBundle\Entity\Media $media)
    {
        $this->media[] = $media;
    
        return $this;
    }

    /**
     * Remove media
     *
     * @param \HealthCareAbroad\MediaBundle\Entity\Media $media
     */
    public function removeMedia(\HealthCareAbroad\MediaBundle\Entity\Media $media)
    {
        $this->media->removeElement($media);
    }

    /**
     * Get media
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMedia()
    {
        return $this->media;
    }

    /**
     * Add doctors
     *
     * @param \HealthCareAbroad\DoctorBundle\Entity\Doctor $doctors
     * @return InstitutionMedicalCenter
     */
    public function addDoctor(\HealthCareAbroad\DoctorBundle\Entity\Doctor $doctors)
    {
        $this->doctors[] = $doctors;
    
        return $this;
    }

    /**
     * Remove doctors
     *
     * @param \HealthCareAbroad\DoctorBundle\Entity\Doctor $doctors
     */
    public function removeDoctor(\HealthCareAbroad\DoctorBundle\Entity\Doctor $doctors)
    {
        $this->doctors->removeElement($doctors);
    }

    /**
     * Get doctors
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDoctors()
    {
        return $this->doctors;
    }

    /**
     * Add contactDetails
     *
     * @param \HealthCareAbroad\HelperBundle\Entity\ContactDetail $contactDetails
     * @return InstitutionMedicalCenter
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