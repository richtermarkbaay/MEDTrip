<?php

namespace HealthCareAbroad\StatisticsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * InstitutionStatisticsDaily
 */
class InstitutionStatisticsDaily extends StatisticsDaily
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
    private $institutionId;

    /**
     * @var integer
     */
    private $categoryId;

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
     * @return InstitutionStatisticsDaily
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
     * Set institutionId
     *
     * @param integer $institutionId
     * @return InstitutionStatisticsDaily
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
     * @return InstitutionStatisticsDaily
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
     * Set ipAddress
     *
     * @param string $ipAddress
     * @return InstitutionStatisticsDaily
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