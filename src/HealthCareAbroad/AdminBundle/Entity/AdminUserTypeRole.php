<?php

namespace HealthCareAbroad\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HealthCareAbroad\AdminBundle\Entity\AdminUserTypeRole
 */
class AdminUserTypeRole
{
    /**
     * @var integer $adminUserTypeId
     */
    private $adminUserTypeId;

    /**
     * @var integer $adminUserRoleId
     */
    private $adminUserRoleId;


    /**
     * Set adminUserTypeId
     *
     * @param integer $adminUserTypeId
     * @return AdminUserTypeRole
     */
    public function setAdminUserTypeId($adminUserTypeId)
    {
        $this->adminUserTypeId = $adminUserTypeId;
        return $this;
    }

    /**
     * Get adminUserTypeId
     *
     * @return integer 
     */
    public function getAdminUserTypeId()
    {
        return $this->adminUserTypeId;
    }

    /**
     * Set adminUserRoleId
     *
     * @param integer $adminUserRoleId
     * @return AdminUserTypeRole
     */
    public function setAdminUserRoleId($adminUserRoleId)
    {
        $this->adminUserRoleId = $adminUserRoleId;
        return $this;
    }

    /**
     * Get adminUserRoleId
     *
     * @return integer 
     */
    public function getAdminUserRoleId()
    {
        return $this->adminUserRoleId;
    }
}