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
     * @var integer
     */
    private $total;

    /**
     * @var \HealthCareAbroad\StatisticsBundle\Entity\InstitutionMedicalCenterStatisticsAnnualIpAddresses
     */
    private $institutionMedicalCenterStatisticsAnnualIpAddresses;


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
     * Set institutionMedicalCenterStatisticsAnnualIpAddresses
     *
     * @param \HealthCareAbroad\StatisticsBundle\Entity\InstitutionMedicalCenterStatisticsAnnualIpAddresses $institutionMedicalCenterStatisticsAnnualIpAddresses
     * @return InstitutionMedicalCenterStatisticsAnnual
     */
    public function setInstitutionMedicalCenterStatisticsAnnualIpAddresses(\HealthCareAbroad\StatisticsBundle\Entity\InstitutionMedicalCenterStatisticsAnnualIpAddresses $institutionMedicalCenterStatisticsAnnualIpAddresses = null)
    {
        $this->institutionMedicalCenterStatisticsAnnualIpAddresses = $institutionMedicalCenterStatisticsAnnualIpAddresses;
    
        return $this;
    }

    /**
     * Get institutionMedicalCenterStatisticsAnnualIpAddresses
     *
     * @return \HealthCareAbroad\StatisticsBundle\Entity\InstitutionMedicalCenterStatisticsAnnualIpAddresses 
     */
    public function getInstitutionMedicalCenterStatisticsAnnualIpAddresses()
    {
        return $this->institutionMedicalCenterStatisticsAnnualIpAddresses;
    }
}