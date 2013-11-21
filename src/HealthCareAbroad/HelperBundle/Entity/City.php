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
    private $institutionId;

    /**
     * @var integer
     */
    private $status;

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
     * Set institutionId
     *
     * @param integer $institutionId
     * @return City
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