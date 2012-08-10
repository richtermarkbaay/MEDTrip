<?php

namespace HealthCareAbroad\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HealthCareAbroad\UserBundle\Entity\AdminUser
 */
class AdminUser extends SiteUser
{
    /**
     * @var integer $status
     */
    private $status;

    /**
     * @var HealthCareAbroad\UserBundle\Entity\AdminUserType
     */
    private $adminUserType;

    /**
     * Set status
     *
     * @param boolean $status
     * @return AdminUser
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
     * Set adminUserType
     *
     * @param HealthCareAbroad\UserBundle\Entity\AdminUserType $adminUserType
     * @return AdminUser
     */
    public function setAdminUserType(\HealthCareAbroad\UserBundle\Entity\AdminUserType $adminUserType = null)
    {
        $this->adminUserType = $adminUserType;
        return $this;
    }

    /**
     * Get adminUserType
     *
     * @return HealthCareAbroad\UserBundle\Entity\AdminUserType 
     */
    public function getAdminUserType()
    {
        return $this->adminUserType;
    }
}