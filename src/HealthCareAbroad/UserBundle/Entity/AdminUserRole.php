<?php

namespace HealthCareAbroad\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HealthCareAbroad\UserBundle\Entity\AdminUserRole
 */
class AdminUserRole
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
     * @var boolean $status
     */
    private $status;

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
}