<?php

namespace HealthCareAbroad\HelperBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HealthCareAbroad\HelperBundle\Entity\ContactDetail
 */
class ContactDetail
{
    
    const TYPE_PHONE = 1;
    const TYPE_MOBILE = 2;
    const TYPE_FAX = 3;
    
    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $type;

    /**
     * @var string
     */
    private $number;

    /**
     * @var string
     */
    private $countryCode;

    /**
     * @var string
     */
    private $areaCode;

    /**
     * @var string
     */
    private $abbr;
    
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
     * Set type
     *
     * @param integer $type
     * @return ContactDetail
     */
    public function setType($type)
    {
        $this->type = $type;
    
        return $this;
    }

    /**
     * Get type
     *
     * @return integer 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set number
     *
     * @param string $number
     * @return ContactDetail
     */
    public function setNumber($number)
    {
        $this->number = $number;
    
        return $this;
    }

    /**
     * Get number
     *
     * @return string 
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Set countryCode
     *
     * @param string $countryCode
     * @return ContactDetail
     */
    public function setCountryCode($countryCode)
    {
        $this->countryCode = $countryCode;
    
        return $this;
    }

    /**
     * Get countryCode
     *
     * @return string 
     */
    public function getCountryCode()
    {
        return $this->countryCode;
    }

    /**
     * Set areaCode
     *
     * @param string $areaCode
     * @return ContactDetail
     */
    public function setAreaCode($areaCode)
    {
        $this->areaCode = $areaCode;
    
        return $this;
    }

    /**
     * Get areaCode
     *
     * @return string 
     */
    public function getAreaCode()
    {
        return $this->areaCode;
    }
    
    /**
     * Set abbr
     *
     * @param string $abbr
     * @return ContactDetail
     */
    public function setAbbr($abbr)
    {
        $this->abbr = $abbr;
    
        return $this;
    }
    
    /**
     * Get abbr
     *
     * @return string
     */
    public function getAbbr()
    {
        return $this->abbr;
    }

}