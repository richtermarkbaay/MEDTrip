<?php

namespace HealthCareAbroad\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HealthCareAbroad\AdminBundle\Entity\AdminUser
 */
class AdminUser
{
    /**
     * @var bigint $accountId
     */
    private $accountId;

    /**
     * @var boolean $status
     */
    private $status;

    /**
     * @var HealthCareAbroad\AdminBundle\Entity\AdminUserTypes
     */
    private $adminUserType;


    /**
     * Get accountId
     *
     * @return bigint 
     */
    public function getAccountId()
    {
        return $this->accountId;
    }

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
     * @param HealthCareAbroad\AdminBundle\Entity\AdminUserTypes $adminUserType
     * @return AdminUser
     */
    public function setAdminUserType(\HealthCareAbroad\AdminBundle\Entity\AdminUserTypes $adminUserType = null)
    {
        $this->adminUserType = $adminUserType;
        return $this;
    }

    /**
     * Get adminUserType
     *
     * @return HealthCareAbroad\AdminBundle\Entity\AdminUserTypes 
     */
    public function getAdminUserType()
    {
        return $this->adminUserType;
    }
}