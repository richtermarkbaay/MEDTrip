<?php

namespace HealthCareAbroad\StatisticsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * InstitutionMedicalCenterStatisticsAnnual
 */
class InstitutionMedicalCenterStatisticsAnnual
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
     * @var boolean
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
    private $total;

    /**
     * @var \HealthCareAbroad\StatisticsBundle\Entity\InstitutionMedicalCenterStatisticsAnnualIpAddress
     */
    private $institutionMedicalCenterStatisticsAnnualIpAddress;


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
     * @return InstitutionMedicalCenterStatisticsAnnual
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
     * @param boolean $categoryId
     * @return InstitutionMedicalCenterStatisticsAnnual
     */
    public function setCategoryId($categoryId)
    {
        $this->categoryId = $categoryId;
    
        return $this;
    }

    /**
     * Get categoryId
     *
     * @return boolean 
     */
    public function getCategoryId()
    {
        return $this->categoryId;
    }

    /**
     * Set institutionId
     *
     * @param integer $institutionId
     * @return InstitutionMedicalCenterStatisticsAnnual
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
     * @return InstitutionMedicalCenterStatisticsAnnual
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
     * Set total
     *
     * @param integer $total
     * @return InstitutionMedicalCenterStatisticsAnnual
     */
    public function setTotal($total)
    {
        $this->total = $total;
    
        return $this;
    }

    /**
     * Get total
     *
     * @return integer 
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * Set institutionMedicalCenterStatisticsAnnualIpAddress
     *
     * @param \HealthCareAbroad\StatisticsBundle\Entity\InstitutionMedicalCenterStatisticsAnnualIpAddress $institutionMedicalCenterStatisticsAnnualIpAddress
     * @return InstitutionMedicalCenterStatisticsAnnual
     */
    public function setInstitutionMedicalCenterStatisticsAnnualIpAddress(\HealthCareAbroad\StatisticsBundle\Entity\InstitutionMedicalCenterStatisticsAnnualIpAddress $institutionMedicalCenterStatisticsAnnualIpAddress = null)
    {
        $this->institutionMedicalCenterStatisticsAnnualIpAddress = $institutionMedicalCenterStatisticsAnnualIpAddress;
    
        return $this;
    }

    /**
     * Get institutionMedicalCenterStatisticsAnnualIpAddress
     *
     * @return \HealthCareAbroad\StatisticsBundle\Entity\InstitutionMedicalCenterStatisticsAnnualIpAddress 
     */
    public function getInstitutionMedicalCenterStatisticsAnnualIpAddress()
    {
        return $this->institutionMedicalCenterStatisticsAnnualIpAddress;
    }
}
