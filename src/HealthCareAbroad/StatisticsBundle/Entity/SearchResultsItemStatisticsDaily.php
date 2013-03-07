<?php

namespace HealthCareAbroad\StatisticsBundle\Entity;

class SearchResultsItemStatisticsDaily extends StatisticsDaily
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var \DateTime
     */
    private $date;

    /**
     * @var integer
     */
    private $categoryId;

    /**
     * @var integer
     */
    private $institutionId;

    /**
     * @var integer
     */
    private $institutionMedicalCenterId;

    /**
     * @var integer
     */
    private $countryId;

    /**
     * @var integer
     */
    private $cityId;

    /**
     * @var integer
     */
    private $specializationId;

    /**
     * @var integer
     */
    private $subSpecializationId;

    /**
     * @var integer
     */
    private $treatmentId;


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
     * Set date
     *
     * @param \DateTime $date
     * @return SearchResultsItemStatisticsDaily
     */
    public function setDate($date)
    {
        $this->date = $date;
    
        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set categoryId
     *
     * @param integer $categoryId
     * @return SearchResultsItemStatisticsDaily
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
     * Set institutionId
     *
     * @param integer $institutionId
     * @return SearchResultsItemStatisticsDaily
     */
    public function setInstitutionId($institutionId)
    {
        $this->institutionId = $institutionId;
    
        return $this;
    }

    /**
     * Get institutionId
     *
     * @return integer 
     */
    public function getInstitutionId()
    {
        return $this->institutionId;
    }

    /**
     * Set institutionMedicalCenterId
     *
     * @param integer $institutionMedicalCenterId
     * @return SearchResultsItemStatisticsDaily
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
     * @return SearchResultsItemStatisticsDaily
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
     * @return SearchResultsItemStatisticsDaily
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
     * Set specializationId
     *
     * @param integer $specializationId
     * @return SearchResultsItemStatisticsDaily
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
     * Set subSpecializationId
     *
     * @param integer $subSpecializationId
     * @return SearchResultsItemStatisticsDaily
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
     * Set treatmentId
     *
     * @param integer $treatmentId
     * @return SearchResultsItemStatisticsDaily
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
}