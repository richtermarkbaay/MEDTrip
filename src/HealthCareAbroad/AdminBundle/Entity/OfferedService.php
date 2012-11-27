<?php

namespace HealthCareAbroad\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HealthCareAbroad\AdminBundle\Entity\OfferedService
 */
class OfferedService
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
     * @var boolean $status
     */
    private $status;

    /**
     * @var datetime $dateCreated
     */
    private $dateCreated;


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
     * @return OfferedService
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
     * @return OfferedService
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
     * Set dateCreated
     *
     * @param datetime $dateCreated
     * @return OfferedService
     */
    public function setDateCreated($dateCreated)
    {
        $this->dateCreated = $dateCreated;
        return $this;
    }

    /**
     * Get dateCreated
     *
     * @return datetime 
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
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
     * @return OfferedService
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
    
    public function __toString()
    {
        return $this->name;
    }
}