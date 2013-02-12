<?php

namespace HealthCareAbroad\StatisticsBundle\Entity;

class InstitutionMedicalCenterStatisticsDaily extends StatisticsDaily
{
    
    /**
     * @var bigint $id
     */
    private $id;

    /**
     * @var date $date
     */
    private $date;

    /**
     * @var integer $categoryId
     */
    private $categoryId;

    /**
     * @var bigint $advertisementId
     */
    private $advertisementId;

    /**
     * @var bigint $institutionId
     */
    private $institutionId;

    /**
     * @var bigint $institutionMedicalCenterId
     */
    private $institutionMedicalCenterId;

    /**
     * @var bigint $countryId
     */
    private $countryId;

    /**
     * @var bigint $cityId
     */
    private $cityId;

    /**
     * @var bigint $specializationId
     */
    private $specializationId;

    /**
     * @var bigint $subSpecializationId
     */
    private $subSpecializationId;

    /**
     * @var bigint $treatmentId
     */
    private $treatmentId;


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
     * Set date
     *
     * @param date $date
     * @return InstitutionMedicalCenterStatisticsDaily
     */
    public function setDate($date)
    {
        $this->date = $date;
        return $this;
    }

    /**
     * Get date
     *
     * @return date 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set categoryId
     *
     * @param integer $categoryId
     * @return InstitutionMedicalCenterStatisticsDaily
     */
    public function setCategoryId($categoryId)
    {
        $this->categoryId = $categoryId;
        return $this;
    }

    /**
     * Get categoryId
     *
     * @return integer 
     */
    public function getCategoryId()
    {
        return $this->categoryId;
    }

    /**
     * Set advertisementId
     *
     * @param bigint $advertisementId
     * @return InstitutionMedicalCenterStatisticsDaily
     */
    public function setAdvertisementId($advertisementId)
    {
        $this->advertisementId = $advertisementId;
        return $this;
    }

    /**
     * Get advertisementId
     *
     * @return bigint 
     */
    public function getAdvertisementId()
    {
        return $this->advertisementId;
    }

    /**
     * Set institutionId
     *
     * @param bigint $institutionId
     * @return InstitutionMedicalCenterStatisticsDaily
     */
    public function setInstitutionId($institutionId)
    {
        $this->institutionId = $institutionId;
        return $this;
    }

    /**
     * Get institutionId
     *
     * @return bigint 
     */
    public function getInstitutionId()
    {
        return $this->institutionId;
    }

    /**
     * Set institutionMedicalCenterId
     *
     * @param bigint $institutionMedicalCenterId
     * @return InstitutionMedicalCenterStatisticsDaily
     */
    public function setInstitutionMedicalCenterId($institutionMedicalCenterId)
    {
        $this->institutionMedicalCenterId = $institutionMedicalCenterId;
        return $this;
    }

    /**
     * Get institutionMedicalCenterId
     *
     * @return bigint 
     */
    public function getInstitutionMedicalCenterId()
    {
        return $this->institutionMedicalCenterId;
    }

    /**
     * Set countryId
     *
     * @param bigint $countryId
     * @return InstitutionMedicalCenterStatisticsDaily
     */
    public function setCountryId($countryId)
    {
        $this->countryId = $countryId;
        return $this;
    }

    /**
     * Get countryId
     *
     * @return bigint 
     */
    public function getCountryId()
    {
        return $this->countryId;
    }

    /**
     * Set cityId
     *
     * @param bigint $cityId
     * @return InstitutionMedicalCenterStatisticsDaily
     */
    public function setCityId($cityId)
    {
        $this->cityId = $cityId;
        return $this;
    }

    /**
     * Get cityId
     *
     * @return bigint 
     */
    public function getCityId()
    {
        return $this->cityId;
    }

    /**
     * Set specializationId
     *
     * @param bigint $specializationId
     * @return InstitutionMedicalCenterStatisticsDaily
     */
    public function setSpecializationId($specializationId)
    {
        $this->specializationId = $specializationId;
        return $this;
    }

    /**
     * Get specializationId
     *
     * @return bigint 
     */
    public function getSpecializationId()
    {
        return $this->specializationId;
    }

    /**
     * Set subSpecializationId
     *
     * @param bigint $subSpecializationId
     * @return InstitutionMedicalCenterStatisticsDaily
     */
    public function setSubSpecializationId($subSpecializationId)
    {
        $this->subSpecializationId = $subSpecializationId;
        return $this;
    }

    /**
     * Get subSpecializationId
     *
     * @return bigint 
     */
    public function getSubSpecializationId()
    {
        return $this->subSpecializationId;
    }

    /**
     * Set treatmentId
     *
     * @param bigint $treatmentId
     * @return InstitutionMedicalCenterStatisticsDaily
     */
    public function setTreatmentId($treatmentId)
    {
        $this->treatmentId = $treatmentId;
        return $this;
    }

    /**
     * Get treatmentId
     *
     * @return bigint 
     */
    public function getTreatmentId()
    {
        return $this->treatmentId;
    }
}