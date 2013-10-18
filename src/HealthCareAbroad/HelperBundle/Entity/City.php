<?php

namespace HealthCareAbroad\HelperBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HealthCareAbroad\HelperBundle\Entity\City
 */
class City
{
    const STATUS_NEW = 2;
    
    const STATUS_ACTIVE = 1;
    
    const STATUS_INACTIVE = 0;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $slug;

    /**
     * @var integer
     */
    private $status;

    /**
     * @var integer
     */
    private $geoCityId;

    /**
     * @var integer
     */
    private $oldId;

    /**
     * @var \HealthCareAbroad\HelperBundle\Entity\State
     */
    private $state;

    /**
     * @var \HealthCareAbroad\HelperBundle\Entity\Country
     */
    private $country;


    /**
     * Set id
     *
     * @param integer $id
     * @return City
     */
    public function setId($id)
    {
        $this->id = $id;
    
        return $this;
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
     * Set name
     *
     * @param string $name
     * @return City
     */
    public function setName($name)
    {
        $this->name = $name;
    
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set slug
     *
     * @param string $slug
     * @return City
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
    
        return $this;
    }

    /**
     * Get slug
     *
     * @return string 
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return City
     */
    public function setStatus($status)
    {
        $this->status = $status;
    
        return $this;
    }

    /**
     * Get status
     *
     * @return integer 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set geoCityId
     *
     * @param integer $geoCityId
     * @return City
     */
    public function setGeoCityId($geoCityId)
    {
        $this->geoCityId = $geoCityId;
    
        return $this;
    }

    /**
     * Get geoCityId
     *
     * @return integer 
     */
    public function getGeoCityId()
    {
        return $this->geoCityId;
    }

    /**
     * Set oldId
     *
     * @param integer $oldId
     * @return City
     */
    public function setOldId($oldId)
    {
        $this->oldId = $oldId;
    
        return $this;
    }

    /**
     * Get oldId
     *
     * @return integer 
     */
    public function getOldId()
    {
        return $this->oldId;
    }

    /**
     * Set state
     *
     * @param \HealthCareAbroad\HelperBundle\Entity\State $state
     * @return City
     */
    public function setState(\HealthCareAbroad\HelperBundle\Entity\State $state = null)
    {
        $this->state = $state;
    
        return $this;
    }

    /**
     * Get state
     *
     * @return \HealthCareAbroad\HelperBundle\Entity\State 
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set country
     *
     * @param \HealthCareAbroad\HelperBundle\Entity\Country $country
     * @return City
     */
    public function setCountry(\HealthCareAbroad\HelperBundle\Entity\Country $country = null)
    {
        $this->country = $country;
    
        return $this;
    }

    /**
     * Get country
     *
     * @return \HealthCareAbroad\HelperBundle\Entity\Country 
     */
    public function getCountry()
    {
        return $this->country;
    }

    public function __toString()
    {
        return $this->name;
    }
}