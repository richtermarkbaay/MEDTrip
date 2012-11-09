<?php

namespace HealthCareAbroad\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HealthCareAbroad\UserBundle\Entity\InstitutionUserRole
 */
class InstitutionUserRole
{
    const SUPER_ADMIN = 'SUPER_ADMIN';

    /**
     * Roles that are built-in to the system and can only be assigned to user types by directly editing in db
     */
    const STATUS_BUILT_IN_ROLE = 1;

    /**
     * Active roles that can be assigned to a user type
     */
    const STATUS_ACTIVE = 2;

    /**
     * Inactive user roles and cannot be assigned to a user type
     */
    const STATUS_INACTIVE = 4;

    /**
     * @var integer $id
     */
    private $id;

    /**
     * @var string $name
     */
    private $name;

    /**
     * @var string $description
     */
    private $description;

    /**
     * @var smallint $status
     */
    private $status;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $institutionUserTypes;

    public function __construct()
    {
        $this->institutionUserTypes = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return InstitutionUserRole
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
     * Set description
     *
     * @param string $description
     * @return InstitutionUserRole
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set status
     *
     * @param smallint $status
     * @return InstitutionUserRole
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
     * Add institutionUserType
     *
     * @param HealthCareAbroad\UserBundle\Entity\InstitutionUserType $institutionUserType
     * @return InstitutionUserRole
     */
    public function addInstitutionUserType(\HealthCareAbroad\UserBundle\Entity\InstitutionUserType $institutionUserType)
    {
        $this->institutionUserType[] = $institutionUserType;
        return $this;
    }

    /**
     * Remove institutionUserType
     *
     * @param <variableType$institutionUserType
     */
    public function removeInstitutionUserType(\HealthCareAbroad\UserBundle\Entity\InstitutionUserType $institutionUserType)
    {
        $this->institutionUserType->removeElement($institutionUserType);
    }

    /**
     * Get institutionUserType
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getInstitutionUserTypes()
    {
        return $this->institutionUserTypes;
    }
}