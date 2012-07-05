<?php

namespace HealthCareAbroad\ProviderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HealthCareAbroad\ProviderBundle\Entity\ProviderUserTypeRole
 */
class ProviderUserTypeRole
{
    /**
     * @var integer $providerUserTypeId
     */
    private $providerUserTypeId;

    /**
     * @var integer $providerUserRoleId
     */
    private $providerUserRoleId;


    /**
     * Set providerUserTypeId
     *
     * @param integer $providerUserTypeId
     * @return ProviderUserTypeRole
     */
    public function setProviderUserTypeId($providerUserTypeId)
    {
        $this->providerUserTypeId = $providerUserTypeId;
        return $this;
    }

    /**
     * Get providerUserTypeId
     *
     * @return integer 
     */
    public function getProviderUserTypeId()
    {
        return $this->providerUserTypeId;
    }

    /**
     * Set providerUserRoleId
     *
     * @param integer $providerUserRoleId
     * @return ProviderUserTypeRole
     */
    public function setProviderUserRoleId($providerUserRoleId)
    {
        $this->providerUserRoleId = $providerUserRoleId;
        return $this;
    }

    /**
     * Get providerUserRoleId
     *
     * @return integer 
     */
    public function getProviderUserRoleId()
    {
        return $this->providerUserRoleId;
    }
}