<?php
namespace HealthCareAbroad\StatisticsBundle\Entity;

class AdvertisementStatisticsDaily extends StatisticsDaily
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
     * @var bigint $advertisementId
     */
    private $advertisementId;

    /**
     * @var integer $institutionId
     */
    private $institutionId;

    /**
     * @var integer $categoryId
     */
    private $categoryId;


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
     * @return AdvertisementStatisticsDaily
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
     * Set advertisementId
     *
     * @param bigint $advertisementId
     * @return AdvertisementStatisticsDaily
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
     * @param integer $institutionId
     * @return AdvertisementStatisticsDaily
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
     * @return AdvertisementStatisticsDaily
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
}