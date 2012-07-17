<?php

namespace HealthCareAbroad\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HealthCareAbroad\UserBundle\Entity\ProviderUserType
 */
class ProviderUserType
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
     * @var HealthCareAbroad\ProviderBundle\Entity\Provider
     */
    private $provider;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $providerUserRole;

    public function __construct()
    {
        $this->providerUserRole = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return ProviderUserType
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
     * @return ProviderUserType
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
     * @return ProviderUserType
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
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $providerUserRoles;


    /**
     * Add providerUserRoles
     *
     * @param HealthCareAbroad\UserBundle\Entity\ProviderUserRole $providerUserRoles
     * @return ProviderUserType
     */
    public function addProviderUserRole(\HealthCareAbroad\UserBundle\Entity\ProviderUserRole $providerUserRoles)
    {
        $this->providerUserRoles[] = $providerUserRoles;
        return $this;
    }

    /**
     * Remove providerUserRoles
     *
     * @param <variableType$providerUserRoles
     */
    public function removeProviderUserRole(\HealthCareAbroad\UserBundle\Entity\ProviderUserRole $providerUserRoles)
    {
        $this->providerUserRoles->removeElement($providerUserRoles);
    }

    /**
     * Get providerUserRoles
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getProviderUserRoles()
    {
        return $this->providerUserRoles;
    }
}