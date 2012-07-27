<?php

namespace HealthCareAbroad\HelperBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HealthCareAbroad\HelperBundle\Entity\ContactDetail
 */
class ContactDetail
{
    /**
     * @var bigint $id
     */
    private $id;

    /**
     * @var smallint $type
     */
    private $type;

    /**
     * @var string $value
     */
    private $value;

    /**
     * @var smallint $status
     */
    private $status;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $institution;

    public function __construct()
    {
        $this->institution = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Get id
     *
     * @return bigint 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set type
     *
     * @param smallint $type
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
     * @return smallint 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set value
     *
     * @param string $value
     * @return ContactDetail
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * Get value
     *
     * @return string 
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set status
     *
     * @param smallint $status
     * @return ContactDetail
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
     * Add institution
     *
     * @param HealthCareAbroad\InstitutionBundle\Entity\Institution $institution
     * @return ContactDetail
     */
    public function addInstitution(\HealthCareAbroad\InstitutionBundle\Entity\Institution $institution)
    {
        $this->institution[] = $institution;
        return $this;
    }

    /**
     * Remove institution
     *
     * @param <variableType$institution
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