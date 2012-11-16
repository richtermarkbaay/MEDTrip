<?php

namespace HealthCareAbroad\HelperBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HealthCareAbroad\HelperBundle\Entity\Affiliation
 */
class Affiliation
{
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
     * @var integer $awardingBodyId
     */
    private $awardingBodyId;

    /**
     * @var integer $country
     */
    private $country;

    /**
     * @var smallint $status
     */
    private $status;

    /**
     * @var string $manyToMany
     */
    private $manyToMany;


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
     * Set awardingBodyId
     *
     * @param integer $awardingBodyId
     * @return Affiliation
     */
    public function setAwardingBodyId($awardingBodyId)
    {
        $this->awardingBodyId = $awardingBodyId;
        return $this;
    }

    /**
     * Get awardingBodyId
     *
     * @return integer 
     */
    public function getAwardingBodyId()
    {
        return $this->awardingBodyId;
    }

    /**
     * Set country
     *
     * @param integer $country
     * @return Affiliation
     */
    public function setCountry($country)
    {
        $this->country = $country;
        return $this;
    }

    /**
     * Get country
     *
     * @return integer 
     */
    public function getCountry()
    {
        return $this->country;
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

    /**
     * Set manyToMany
     *
     * @param string $manyToMany
     * @return Affiliation
     */
    public function setManyToMany($manyToMany)
    {
        $this->manyToMany = $manyToMany;
        return $this;
    }

    /**
     * Get manyToMany
     *
     * @return string 
     */
    public function getManyToMany()
    {
        return $this->manyToMany;
    }
}