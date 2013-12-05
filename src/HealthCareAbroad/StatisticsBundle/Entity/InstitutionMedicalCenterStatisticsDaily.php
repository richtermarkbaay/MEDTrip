<?php

namespace HealthCareAbroad\StatisticsBundle\Entity;

class InstitutionMedicalCenterStatisticsDaily extends StatisticsDaily
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var date
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
     * @var string
     */
    private $ipAddress;


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
     * Set institutionId
     *
     * @param integer $institutionId
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
     * @return integer 
     */
    public function getInstitutionMedicalCenterId()
    {
        return $this->institutionMedicalCenterId;
    }

    /**
     * Set ipAddress
     *
     * @param string $ipAddress
     * @return InstitutionMedicalCenterStatisticsDaily
     */
    public function setIpAddress($ipAddress)
    {
        $this->ipAddress = $ipAddress;
    
        return $this;
    }

    /**
     * Get ipAddress
     *
     * @return string 
     */
    public function getIpAddress()
    {
        return $this->ipAddress;
    }
}