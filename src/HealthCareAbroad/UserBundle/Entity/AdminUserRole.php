<?php

namespace HealthCareAbroad\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HealthCareAbroad\UserBundle\Entity\AdminUserRole
 */
class AdminUserRole
{
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
     * @var integer $status
     */
    private $status;
    
    /**
     * @var unknown_type
     */
    private $bitMask;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $adminUserTypes;

    public function __construct()
    {
        $this->adminUserTypes = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return AdminUserRole
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
     * Set bitMask
     * 
     * @param integer $bitMask
     * @return AdminUserRole
     */
    public function setBitMask($bitMask)
    {
        $this->bitMask = $bitMask;
        return $this;
    }
    
    /**
     * Get bitMask
     * 
     * @return integer
     */
    public function getBitMask()
    {
        return $this->bitMask;
    }

    /**
     * Set status
     *
     * @param boolean $status
     * @return AdminUserRole
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
     * Add adminUserTypes
     *
     * @param HealthCareAbroad\UserBundle\Entity\AdminUserType $adminUserTypes
     * @return AdminUserRole
     */
    public function addAdminUserType(\HealthCareAbroad\UserBundle\Entity\AdminUserType $adminUserTypes)
    {
        $this->adminUserTypes[] = $adminUserTypes;
        return $this;
    }

    /**
     * Remove adminUserTypes
     *
     * @param <variableType$adminUserTypes
     */
    public function removeAdminUserType(\HealthCareAbroad\UserBundle\Entity\AdminUserType $adminUserTypes)
    {
        $this->adminUserTypes->removeElement($adminUserTypes);
    }

    /**
     * Get adminUserTypes
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getAdminUserTypes()
    {
        return $this->adminUserTypes;
    }
    /**
     * @var string $label
     */
    private $label;


    /**
     * Set label
     *
     * @param string $label
     * @return AdminUserRole
     */
    public function setLabel($label)
    {
        $this->label = $label;
        return $this;
    }

    /**
     * Get label
     *
     * @return string 
     */
    public function getLabel()
    {
        return $this->label;
    }
}