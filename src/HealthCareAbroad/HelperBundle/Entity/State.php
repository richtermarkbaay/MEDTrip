<?php

namespace HealthCareAbroad\HelperBundle\Entity;

class State
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
    private $administrativeCode;

    /**
     * @var integer
     */
    private $institutionId;

    /**
     * @var integer
     */
    private $status;

    /**
     * @var \HealthCareAbroad\HelperBundle\Entity\Country
     */
    private $country;


    /**
     * Set id
     *
     * @param integer $id
     * @return State
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
     * @return State
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
     * Set administrativeCode
     *
     * @param string $administrativeCode
     * @return State
     */
    public function setAdministrativeCode($administrativeCode)
    {
        $this->administrativeCode = $administrativeCode;
    
        return $this;
    }

    /**
     * Get administrativeCode
     *
     * @return string 
     */
    public function getAdministrativeCode()
    {
        return $this->administrativeCode;
    }

    /**
     * Set institutionId
     *
     * @param integer $institutionId
     * @return State
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
     * @return State
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
     * Set country
     *
     * @param \HealthCareAbroad\HelperBundle\Entity\Country $country
     * @return State
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