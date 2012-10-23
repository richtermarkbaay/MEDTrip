<?php

namespace HealthCareAbroad\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HealthCareAbroad\UserBundle\Entity\InstitutionUserType
 */
class InstitutionUserType
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
     * @var smallint $status
     */
    private $status;

    /**
     * @var HealthCareAbroad\InstitutionBundle\Entity\Institution
     */
    private $institution;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $institutionUserRole;

    public function __construct()
    {
        $this->institutionUserRole = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return InstitutionUserType
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
     * @param smallint $status
     * @return InstitutionUserType
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
     * Set institution
     *
     * @param HealthCareAbroad\InstitutionBundle\Entity\Institution $institution
     * @return InstitutionUserType
     */
    public function setInstitution(\HealthCareAbroad\InstitutionBundle\Entity\Institution $institution = null)
    {
        $this->institution = $institution;
        return $this;
    }

    /**
     * Get institution
     *
     * @return HealthCareAbroad\InstitutionBundle\Entity\Institution 
     */
    public function getInstitution()
    {
        return $this->institution;
    }

    /**
     * Add institutionUserRole
     *
     * @param HealthCareAbroad\UserBundle\Entity\InstitutionUserRole $institutionUserRole
     * @return InstitutionUserType
     */
    public function addInstitutionUserRole(\HealthCareAbroad\UserBundle\Entity\InstitutionUserRole $institutionUserRole)
    {
        $this->institutionUserRole[] = $institutionUserRole;
        return $this;
    }

    /**
     * Remove institutionUserRole
     *
     * @param <variableType$institutionUserRole
     */
    public function removeInstitutionUserRole(\HealthCareAbroad\UserBundle\Entity\InstitutionUserRole $institutionUserRole)
    {
        $this->institutionUserRole->removeElement($institutionUserRole);
    }

    /**
     * Get institutionUserRole
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getInstitutionUserRole()
    {
        return $this->institutionUserRole;
    }
}