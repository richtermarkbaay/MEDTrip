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
     * @var text $highlightDoctors
     */
    private $highlightDoctors;

    /**
     * @var text $highlightSpecializations
     */
    private $highlightSpecializations;

    /**
     * @var text $highlightSubSpecializations
     */
    private $highlightSubSpecializations;

    /**
     * @var text $highlightTreatments
     */
    private $highlightTreatments;

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
     * @var HealthCareAbroad\AdvertisementBundle\Entity\AdvertisementType
     */
    private $advertisementType;

    /**
     * @var HealthCareAbroad\InstitutionBundle\Entity\Institution
     */
    private $institution;


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
     * Set highlightDoctors
     *
     * @param text $highlightDoctors
     * @return AdvertisementDenormalizedProperty
     */
    public function setHighlightDoctors($highlightDoctors)
    {
        $this->highlightDoctors = $highlightDoctors;
        return $this;
    }

    /**
     * Get highlightDoctors
     *
     * @return text 
     */
    public function getHighlightDoctors()
    {
        return $this->highlightDoctors;
    }

    /**
     * Set highlightSpecializations
     *
     * @param text $highlightSpecializations
     * @return AdvertisementDenormalizedProperty
     */
    public function setHighlightSpecializations($highlightSpecializations)
    {
        $this->highlightSpecializations = $highlightSpecializations;
        return $this;
    }

    /**
     * Get highlightSpecializations
     *
     * @return text 
     */
    public function getHighlightSpecializations()
    {
        return $this->highlightSpecializations;
    }

    /**
     * Set highlightSubSpecializations
     *
     * @param text $highlightSubSpecializations
     * @return AdvertisementDenormalizedProperty
     */
    public function setHighlightSubSpecializations($highlightSubSpecializations)
    {
        $this->highlightSubSpecializations = $highlightSubSpecializations;
        return $this;
    }

    /**
     * Get highlightSubSpecializations
     *
     * @return text 
     */
    public function getHighlightSubSpecializations()
    {
        return $this->highlightSubSpecializations;
    }

    /**
     * Set highlightTreatments
     *
     * @param text $highlightTreatments
     * @return AdvertisementDenormalizedProperty
     */
    public function setHighlightTreatments($highlightTreatments)
    {
        $this->highlightTreatments = $highlightTreatments;
        return $this;
    }

    /**
     * Get highlightTreatments
     *
     * @return text 
     */
    public function getHighlightTreatments()
    {
        return $this->highlightTreatments;
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
}