<?php

namespace HealthCareAbroad\HelperBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HealthCareAbroad\HelperBundle\Entity\ContactDetail
 */
class ContactDetail
{
    
    
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
     * @var integer
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
    
    public function __toString()
    {
        return ($this->countryCode ? '+'.$this->countryCode:'').'-'.$this->areaCode.'-'.$this->number;
    }


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
    /**
     * @var boolean
     */
    private $fromNewWidget;


    /**
     * Set fromNewWidget
     *
     * @param boolean $fromNewWidget
     * @return ContactDetail
     */
    public function setFromNewWidget($fromNewWidget)
    {
        $this->fromNewWidget = $fromNewWidget;
    
        return $this;
    }

    /**
     * Get fromNewWidget
     *
     * @return boolean 
     */
    public function getFromNewWidget()
    {
        return $this->fromNewWidget;
    }
    /**
     * @var boolean
     */
    private $isInvalid;


    /**
     * Set isInvalid
     *
     * @param boolean $isInvalid
     * @return ContactDetail
     */
    public function setIsInvalid($isInvalid)
    {
        $this->isInvalid = $isInvalid;
    
        return $this;
    }

    /**
     * Get isInvalid
     *
     * @return boolean 
     */
    public function getIsInvalid()
    {
        return $this->isInvalid;
    }
}