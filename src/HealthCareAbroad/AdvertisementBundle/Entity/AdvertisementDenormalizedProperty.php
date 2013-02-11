<?php

namespace HealthCareAbroad\AdvertisementBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HealthCareAbroad\AdvertisementBundle\Entity\AdvertisementDenormalizedProperty
 */
class AdvertisementDenormalizedProperty
{

    /**
     * @var bigint $id
     */
    private $id;

    /**
     * @var string $title
     */
    private $title;

    /**
     * @var text $description
     */
    private $description;

    /**
     * @var integer $treatmentId
     */
    private $treatmentId;

    /**
     * @var integer $subSpecializationId
     */
    private $subSpecializationId;

    /**
     * @var integer $specializationId
     */
    private $specializationId;

    /**
     * @var integer $institutionMedicalCenterId
     */
    private $institutionMedicalCenterId;

    /**
     * @var integer $countryId
     */
    private $countryId;

    /**
     * @var integer $cityId
     */
    private $cityId;

    /**
     * @var bigint $mediaId
     */
    private $mediaId;

    /**
     * @var string $videoUrl
     */
    private $videoUrl;

    /**
     * @var string $externalUrl
     */
    private $externalUrl;

    /**
     * @var text $highlights
     */
    private $highlights;

    /**
     * @var text $highlightFeaturedImages
     */
    private $highlightFeaturedImages;

    /**
     * @var datetime $dateCreated
     */
    private $dateCreated;

    /**
     * @var datetime $dateExpiry
     */
    private $dateExpiry;

    /**
     * @var smallint $status
     */
    private $status;

    /**
     * @var HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter
     */
    private $institutionMedicalCenter;

    /**
     * @var HealthCareAbroad\TreatmentBundle\Entity\SubSpecialization
     */
    private $subSpecialization;

    /**
     * @var HealthCareAbroad\InstitutionBundle\Entity\Institution
     */
    private $institution;

    /**
     * @var HealthCareAbroad\AdvertisementBundle\Entity\AdvertisementType
     */
    private $advertisementType;

    /**
     * @var HealthCareAbroad\MediaBundle\Entity\Media
     */
    private $media;

    /**
     * @var HealthCareAbroad\HelperBundle\Entity\City
     */
    private $city;

    /**
     * @var HealthCareAbroad\TreatmentBundle\Entity\Treatment
     */
    private $treatment;

    /**
     * @var HealthCareAbroad\TreatmentBundle\Entity\Specialization
     */
    private $specialization;

    /**
     * @var HealthCareAbroad\HelperBundle\Entity\Country
     */
    private $country;


    /**
     * Set id
     *
     * @param bigint $id
     * @return AdvertisementDenormalizedProperty
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
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
     * Set title
     *
     * @param string $title
     * @return AdvertisementDenormalizedProperty
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set description
     *
     * @param text $description
     * @return AdvertisementDenormalizedProperty
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
     * Set treatmentId
     *
     * @param integer $treatmentId
     * @return AdvertisementDenormalizedProperty
     */
    public function setTreatmentId($treatmentId)
    {
        $this->treatmentId = $treatmentId;
        return $this;
    }

    /**
     * Get treatmentId
     *
     * @return integer 
     */
    public function getTreatmentId()
    {
        return $this->treatmentId;
    }

    /**
     * Set subSpecializationId
     *
     * @param integer $subSpecializationId
     * @return AdvertisementDenormalizedProperty
     */
    public function setSubSpecializationId($subSpecializationId)
    {
        $this->subSpecializationId = $subSpecializationId;
        return $this;
    }

    /**
     * Get subSpecializationId
     *
     * @return integer 
     */
    public function getSubSpecializationId()
    {
        return $this->subSpecializationId;
    }

    /**
     * Set specializationId
     *
     * @param integer $specializationId
     * @return AdvertisementDenormalizedProperty
     */
    public function setSpecializationId($specializationId)
    {
        $this->specializationId = $specializationId;
        return $this;
    }

    /**
     * Get specializationId
     *
     * @return integer 
     */
    public function getSpecializationId()
    {
        return $this->specializationId;
    }

    /**
     * Set institutionMedicalCenterId
     *
     * @param integer $institutionMedicalCenterId
     * @return AdvertisementDenormalizedProperty
     */
    public function setInstitutionMedicalCenterId($institutionMedicalCenterId)
    {
        $this->institutionMedicalCenterId = $institutionMedicalCenterId;
        return $this;
    }

    /**
     * Get institutionMedicalCenterId
     *
     * @return integer 
     */
    public function getInstitutionMedicalCenterId()
    {
        return $this->institutionMedicalCenterId;
    }

    /**
     * Set countryId
     *
     * @param integer $countryId
     * @return AdvertisementDenormalizedProperty
     */
    public function setCountryId($countryId)
    {
        $this->countryId = $countryId;
        return $this;
    }

    /**
     * Get countryId
     *
     * @return integer 
     */
    public function getCountryId()
    {
        return $this->countryId;
    }

    /**
     * Set cityId
     *
     * @param integer $cityId
     * @return AdvertisementDenormalizedProperty
     */
    public function setCityId($cityId)
    {
        $this->cityId = $cityId;
        return $this;
    }

    /**
     * Get cityId
     *
     * @return integer 
     */
    public function getCityId()
    {
        return $this->cityId;
    }

    /**
     * Set mediaId
     *
     * @param bigint $mediaId
     * @return AdvertisementDenormalizedProperty
     */
    public function setMediaId($mediaId)
    {
        $this->mediaId = $mediaId;
        return $this;
    }

    /**
     * Get mediaId
     *
     * @return bigint 
     */
    public function getMediaId()
    {
        return $this->mediaId;
    }

    /**
     * Set videoUrl
     *
     * @param string $videoUrl
     * @return AdvertisementDenormalizedProperty
     */
    public function setVideoUrl($videoUrl)
    {
        $this->videoUrl = $videoUrl;
        return $this;
    }

    /**
     * Get videoUrl
     *
     * @return string 
     */
    public function getVideoUrl()
    {
        return $this->videoUrl;
    }

    /**
     * Set externalUrl
     *
     * @param string $externalUrl
     * @return AdvertisementDenormalizedProperty
     */
    public function setExternalUrl($externalUrl)
    {
        $this->externalUrl = $externalUrl;
        return $this;
    }

    /**
     * Get externalUrl
     *
     * @return string 
     */
    public function getExternalUrl()
    {
        return $this->externalUrl;
    }

    /**
     * Set highlights
     *
     * @param text $highlights
     * @return AdvertisementDenormalizedProperty
     */
    public function setHighlights($highlights)
    {
        $this->highlights = $highlights;
        return $this;
    }

    /**
     * Get highlights
     *
     * @return text 
     */
    public function getHighlights()
    {
        return $this->highlights;
    }

    /**
     * Set highlightFeaturedImages
     *
     * @param text $highlightFeaturedImages
     * @return AdvertisementDenormalizedProperty
     */
    public function setHighlightFeaturedImages($highlightFeaturedImages)
    {
        $this->highlightFeaturedImages = $highlightFeaturedImages;
        return $this;
    }

    /**
     * Get highlightFeaturedImages
     *
     * @return text 
     */
    public function getHighlightFeaturedImages()
    {
        return $this->highlightFeaturedImages;
    }

    /**
     * Set dateCreated
     *
     * @param datetime $dateCreated
     * @return AdvertisementDenormalizedProperty
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
     * Set dateExpiry
     *
     * @param datetime $dateExpiry
     * @return AdvertisementDenormalizedProperty
     */
    public function setDateExpiry($dateExpiry)
    {
        $this->dateExpiry = $dateExpiry;
        return $this;
    }

    /**
     * Get dateExpiry
     *
     * @return datetime 
     */
    public function getDateExpiry()
    {
        return $this->dateExpiry;
    }

    /**
     * Set status
     *
     * @param smallint $status
     * @return AdvertisementDenormalizedProperty
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
     * Set institutionMedicalCenter
     *
     * @param HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter $institutionMedicalCenter
     * @return AdvertisementDenormalizedProperty
     */
    public function setInstitutionMedicalCenter(\HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter $institutionMedicalCenter = null)
    {
        $this->institutionMedicalCenter = $institutionMedicalCenter;
        return $this;
    }

    /**
     * Get institutionMedicalCenter
     *
     * @return HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter 
     */
    public function getInstitutionMedicalCenter()
    {
        return $this->institutionMedicalCenter;
    }

    /**
     * Set subSpecialization
     *
     * @param HealthCareAbroad\TreatmentBundle\Entity\SubSpecialization $subSpecialization
     * @return AdvertisementDenormalizedProperty
     */
    public function setSubSpecialization(\HealthCareAbroad\TreatmentBundle\Entity\SubSpecialization $subSpecialization = null)
    {
        $this->subSpecialization = $subSpecialization;
        return $this;
    }

    /**
     * Get subSpecialization
     *
     * @return HealthCareAbroad\TreatmentBundle\Entity\SubSpecialization 
     */
    public function getSubSpecialization()
    {
        return $this->subSpecialization;
    }

    /**
     * Set institution
     *
     * @param HealthCareAbroad\InstitutionBundle\Entity\Institution $institution
     * @return AdvertisementDenormalizedProperty
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
     * Set advertisementType
     *
     * @param HealthCareAbroad\AdvertisementBundle\Entity\AdvertisementType $advertisementType
     * @return AdvertisementDenormalizedProperty
     */
    public function setAdvertisementType(\HealthCareAbroad\AdvertisementBundle\Entity\AdvertisementType $advertisementType = null)
    {
        $this->advertisementType = $advertisementType;
        return $this;
    }

    /**
     * Get advertisementType
     *
     * @return HealthCareAbroad\AdvertisementBundle\Entity\AdvertisementType 
     */
    public function getAdvertisementType()
    {
        return $this->advertisementType;
    }

    /**
     * Set media
     *
     * @param HealthCareAbroad\MediaBundle\Entity\Media $media
     * @return AdvertisementDenormalizedProperty
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
     * Set city
     *
     * @param HealthCareAbroad\HelperBundle\Entity\City $city
     * @return AdvertisementDenormalizedProperty
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
     * Set treatment
     *
     * @param HealthCareAbroad\TreatmentBundle\Entity\Treatment $treatment
     * @return AdvertisementDenormalizedProperty
     */
    public function setTreatment(\HealthCareAbroad\TreatmentBundle\Entity\Treatment $treatment = null)
    {
        $this->treatment = $treatment;
        return $this;
    }

    /**
     * Get treatment
     *
     * @return HealthCareAbroad\TreatmentBundle\Entity\Treatment 
     */
    public function getTreatment()
    {
        return $this->treatment;
    }

    /**
     * Set specialization
     *
     * @param HealthCareAbroad\TreatmentBundle\Entity\Specialization $specialization
     * @return AdvertisementDenormalizedProperty
     */
    public function setSpecialization(\HealthCareAbroad\TreatmentBundle\Entity\Specialization $specialization = null)
    {
        $this->specialization = $specialization;
        return $this;
    }

    /**
     * Get specialization
     *
     * @return HealthCareAbroad\TreatmentBundle\Entity\Specialization 
     */
    public function getSpecialization()
    {
        return $this->specialization;
    }

    /**
     * Set country
     *
     * @param HealthCareAbroad\HelperBundle\Entity\Country $country
     * @return AdvertisementDenormalizedProperty
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