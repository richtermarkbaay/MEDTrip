<?php

namespace HealthCareAbroad\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HealthCareAbroad\UserBundle\Entity\AdminUserType
 */
class AdminUserType
{
    /**
     * User types that are built-in to the system and therefore not editable
     */
    const STATUS_BUILT_IN = 1;
    
    /**
     * User types that are active
     */
    const STATUS_ACTIVE = 2;
    
    /**
     * User types that are inactive
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
     * @var boolean $status
     */
    private $status;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $adminUserRoles;

    public function __construct()
    {
        $this->adminUserRoles = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return AdminUserType
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
     * @return AdminUserType
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
     * Add adminUserRoles
     *
     * @param HealthCareAbroad\UserBundle\Entity\AdminUserRole $adminUserRoles
     * @return AdminUserType
     */
    public function addAdminUserRole(\HealthCareAbroad\UserBundle\Entity\AdminUserRole $adminUserRoles)
    {
        $this->adminUserRoles[] = $adminUserRoles;
        return $this;
    }

    /**
     * Remove adminUserRoles
     *
     * @param <variableType$adminUserRoles
     */
    public function removeAdminUserRole(\HealthCareAbroad\UserBundle\Entity\AdminUserRole $adminUserRoles)
    {
        $this->adminUserRoles->removeElement($adminUserRoles);
    }

    /**
     * Get adminUserRoles
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getAdminUserRoles()
    {
        return $this->adminUserRoles;
    }
}