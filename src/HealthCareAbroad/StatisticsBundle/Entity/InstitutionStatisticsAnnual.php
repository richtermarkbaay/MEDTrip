<?php

namespace HealthCareAbroad\StatisticsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * InstitutionStatisticsAnnual
 */
class InstitutionStatisticsAnnual
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
    private $institutionId;

    /**
     * @var integer
     */
    private $categoryId;

    /**
     * @var integer
     */
    private $total;

    /**
     * @var \HealthCareAbroad\StatisticsBundle\Entity\InstitutionStatisticsAnnualIpAddresses
     */
    private $institutionStatisticsAnnualIpAddresses;

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
     * @return InstitutionStatisticsAnnual
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
     * Set institutionId
     *
     * @param integer $institutionId
     * @return InstitutionStatisticsAnnual
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
     * Set categoryId
     *
     * @param integer $categoryId
     * @return InstitutionStatisticsAnnual
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
     * Set total
     *
     * @param integer $total
     * @return InstitutionStatisticsAnnual
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
     * Set institutionStatisticsAnnualIpAddresses
     *
     * @param \HealthCareAbroad\StatisticsBundle\Entity\InstitutionStatisticsAnnualIpAddresses $institutionStatisticsAnnualIpAddresses
     * @return InstitutionStatisticsAnnual
     */
    public function setInstitutionStatisticsAnnualIpAddresses(\HealthCareAbroad\StatisticsBundle\Entity\InstitutionStatisticsAnnualIpAddresses $institutionStatisticsAnnualIpAddresses = null)
    {
        $this->institutionStatisticsAnnualIpAddresses = $institutionStatisticsAnnualIpAddresses;
    
        return $this;
    }

    /**
     * Get institutionStatisticsAnnualIpAddresses
     *
     * @return \HealthCareAbroad\StatisticsBundle\Entity\InstitutionStatisticsAnnualIpAddresses 
     */
    public function getInstitutionStatisticsAnnualIpAddresses()
    {
        return $this->institutionStatisticsAnnualIpAddresses;
    }
}