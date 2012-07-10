<?php

namespace HealthCareAbroad\ProviderBundle\Entity;

use HealthCareAbroad\UserBundle\Entity\SiteUserInterface;

use Doctrine\ORM\Mapping as ORM;

/**
 * HealthCareAbroad\ProviderBundle\Entity\ProviderUser
 */
class ProviderUser implements SiteUserInterface
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
     * @var HealthCareAbroad\ProviderBundle\Entity\Providers
     */
    private $provider;

    /**
     * @var HealthCareAbroad\ProviderBundle\Entity\ProviderUserTypes
     */
    private $providerUserType;


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
     * @param HealthCareAbroad\ProviderBundle\Entity\Providers $provider
     * @return ProviderUser
     */
    public function setProvider(\HealthCareAbroad\ProviderBundle\Entity\Providers $provider = null)
    {
        $this->provider = $provider;
        return $this;
    }

    /**
     * Get provider
     *
     * @return HealthCareAbroad\ProviderBundle\Entity\Providers 
     */
    public function getProvider()
    {
        return $this->provider;
    }

    /**
     * Set providerUserType
     *
     * @param HealthCareAbroad\ProviderBundle\Entity\ProviderUserTypes $providerUserType
     * @return ProviderUser
     */
    public function setProviderUserType(\HealthCareAbroad\ProviderBundle\Entity\ProviderUserTypes $providerUserType = null)
    {
        $this->providerUserType = $providerUserType;
        return $this;
    }

    /**
     * Get providerUserType
     *
     * @return HealthCareAbroad\ProviderBundle\Entity\ProviderUserTypes 
     */
    public function getProviderUserType()
    {
        return $this->providerUserType;
    }
    
    /**
     * SiteUserInterface
     */
    public function getId()
    {
        return $this->accountId;
    }
    
    public function getEmail()
    {
        
    }
    
    public function getPassword()
    {
        
    }
}