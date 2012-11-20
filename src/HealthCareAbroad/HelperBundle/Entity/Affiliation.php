<?php

namespace HealthCareAbroad\HelperBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HealthCareAbroad\HelperBundle\Entity\Affiliation
 */
class Affiliation
{
	
	const STATUS_ACTIVE = 1;
	const STATUS_INACTIVE = 0;
    /**
     * @var integer $id
     */
    private $id;

    /**
     * @var string $name
     */
    private $name;

    /**
     * @var string $details
     */
    private $details;

	/**
     * @var HealthCareAbroad\HelperBundle\Entity\Country
     */
    private $country;
    
    /**
     * @var HealthCareAbroad\HelperBundle\Entity\AwardingBody
     */
    private $awardingBody;
    

    /**
     * @var smallint $status
     */
    private $status;

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
     * @return Affiliation
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
     * Set details
     *
     * @param string $details
     * @return Affiliation
     */
    public function setDetails($details)
    {
        $this->details = $details;
        return $this;
    }

    /**
     * Get details
     *
     * @return string 
     */
    public function getDetails()
    {
        return $this->details;
    }


	/**
     * Set country
     *
     * @param HealthCareAbroad\HelperBundle\Entity\Country $country
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
     * @return HealthCareAbroad\HelperBundle\Entity\Country
     */
    public function getCountry()
    {
    	return $this->country;
    }

    /**
     * Get awardingBody
     *
     * @return HealthCareAbroad\HelperBundle\Entity\AwardingBody
     */
    public function getAwardingBody()
    {
    	return $this->awardingBody;
    }
    
    /**
     * Set awardingBody
     *
     * @param HealthCareAbroad\HelperBundle\Entity\AwardingBody $awardingBody
     * @return awardingBody
     */
    public function setAwardingBody(\HealthCareAbroad\HelperBundle\Entity\AwardingBody $awardingBody = null)
    {
    	$this->awardingBody = $awardingBody;
    	return $this;
    }
    
    /**
     * Set status
     *
     * @param smallint $status
     * @return Affiliation
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * Get status
     *
     * @return smallint 
     */
    public function getStatus()
    {
        return $this->status;
    }

}