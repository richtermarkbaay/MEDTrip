<?php

namespace HealthCareAbroad\AdminBundle\Entity;



/**
 * HealthCareAbroad\AdminBundle\Entity\Language
 */
class Language
{
	const STATUS_ACTIVE = 1;
	const STATUS_INACTIVE = 0;
	
    /**
     * @var integer $id
     */
    private $id;

    /**
     * @var string $isoCode
     */
    private $isoCode;

    /**
     * @var string $name
     */
    private $name;

    /**
     * @var boolean $status
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
     * Set isoCode
     *
     * @param string $isoCode
     * @return Language
     */
    public function setIsoCode($isoCode)
    {
        $this->isoCode = $isoCode;
        return $this;
    }

    /**
     * Get isoCode
     *
     * @return string 
     */
    public function getIsoCode()
    {
        return $this->isoCode;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Language
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
     * Set status
     *
     * @param boolean $status
     * @return Language
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * Get status
     *
     * @return boolean 
     */
    public function getStatus()
    {
        return $this->status;
    }
    
/**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $institution;

    public function __construct()
    {
        $this->institution = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Add institution
     *
     * @param HealthCareAbroad\InstitutionBundle\Entity\Institution $institution
     * @return InstitutionLanguagesSpoken
     */
    public function addInstitution(\HealthCareAbroad\InstitutionBundle\Entity\Institution $institution)
    {
        $this->institution[] = $institution;
        return $this;
    }

    /**
     * Remove institution
     *
     * @param HealthCareAbroad\InstitutionBundle\Entity\Institution $institution
     */
    public function removeInstitution(\HealthCareAbroad\InstitutionBundle\Entity\Institution $institution)
    {
        $this->institution->removeElement($institution);
    }

    /**
     * Get institution
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getInstitution()
    {
        return $this->institution;
    }
}