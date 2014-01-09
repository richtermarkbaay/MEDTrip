<?php

namespace HealthCareAbroad\HelperBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HealthCareAbroad\HelperBundle\Entity\Country
 */
class Country
{
    const STATUS_NEW = 2;

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
     * @var string $ccIso
     */
    private $ccIso;

    /**
     * @var string $countryCode
     */
    private $countryCode;

    /**
     * @var string $slug
     */
    private $slug;

    /**
     * @var smallint $status
     */
    private $status;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $cities;

    public function __construct()
    {
        $this->cities = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Set id
     *
     * @param integer $id
     * @return Country
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
     * @return Country
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
     * Set ccIso
     *
     * @param string $ccIso
     * @return Country
     */
    public function setCcIso($ccIso)
    {
        $this->ccIso = $ccIso;

        return $this;
    }

    /**
     * Get ccIso
     *
     * @return string 
     */
    public function getCcIso()
    {
        return $this->ccIso;
    }

    /**
     * Set countryCode
     *
     * @param string $countryCode
     * @return Country
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
     * Set slug
     *
     * @param string $slug
     * @return Country
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
     * @param smallint $status
     * @return Country
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
     * Add cities
     *
     * @param HealthCareAbroad\HelperBundle\Entity\City $cities
     * @return Country
     */
    public function addCities(\HealthCareAbroad\HelperBundle\Entity\City $cities)
    {
        $this->cities[] = $cities;
        return $this;
    }

    /**
     * Remove cities
     *
     * @param HealthCareAbroad\HelperBundle\Entity\City $cities
     */
    public function removeCities(\HealthCareAbroad\HelperBundle\Entity\City $cities)
    {
        $this->cities->removeElement($cities);
    }

    /**
     * Get cities
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getCities()
    {
        return $this->cities;
    }
    
    public function __toString()
    {
        return $this->name;
    }
}