<?php

namespace HealthCareAbroad\UserBundle\Entity;

use HealthCareAbroad\UserBundle\Entity\SiteUser;

use Doctrine\ORM\Mapping as ORM;

/**
 * HealthCareAbroad\ProviderBundle\Entity\ProviderUser
 */
class ProviderUser extends SiteUser
{
    

    /**
     * @var boolean $status
     */
    private $status;

    /**
     * @var HealthCareAbroad\ProviderBundle\Entity\Providers
     */
    private $provider;

    /**
     * @var HealthCareAbroad\ProviderBundle\Entity\ProviderUserTypes
     */
    private $providerUserType;
    
    /**
     * Set status
     *
     * @param boolean $status
     * @return ProviderUser
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
     * Set provider
     *
     * @param HealthCareAbroad\ProviderBundle\Entity\Provider $provider
     * @return ProviderUser
     */
    public function setProvider(\HealthCareAbroad\ProviderBundle\Entity\Provider $provider = null)
    {
        $this->provider = $provider;
        return $this;
    }

    /**
     * Get provider
     *
     * @return HealthCareAbroad\ProviderBundle\Entity\Provider
     */
    public function getProvider()
    {
        return $this->provider;
    }

    /**
     * Set providerUserType
     *
     * @param HealthCareAbroad\ProviderBundle\Entity\ProviderUserType $providerUserType
     * @return ProviderUser
     */
    public function setProviderUserType(\HealthCareAbroad\ProviderBundle\Entity\ProviderUserType $providerUserType = null)
    {
        $this->providerUserType = $providerUserType;
        return $this;
    }

    /**
     * Get providerUserType
     *
     * @return HealthCareAbroad\ProviderBundle\Entity\ProviderUserType
     */
    public function getProviderUserType()
    {
        return $this->providerUserType;
    }
}