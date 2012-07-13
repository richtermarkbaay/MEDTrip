<?php

namespace HealthCareAbroad\ProviderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HealthCareAbroad\ProviderBundle\Entity\ProviderUserRole
 */
class ProviderUserRole
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
     * @var string $description
     */
    private $description;

    /**
     * @var boolean $status
     */
    private $status;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $providerUserType;

    public function __construct()
    {
        $this->providerUserType = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return ProviderUserRole
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
     * Set description
     *
     * @param string $description
     * @return ProviderUserRole
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set status
     *
     * @param boolean $status
     * @return ProviderUserRole
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
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $providerUserTypes;


    /**
     * Add providerUserTypes
     *
     * @param HealthCareAbroad\ProviderBundle\Entity\ProviderUserType $providerUserTypes
     * @return ProviderUserRole
     */
    public function addProviderUserType(\HealthCareAbroad\ProviderBundle\Entity\ProviderUserType $providerUserTypes)
    {
        $this->providerUserTypes[] = $providerUserTypes;
        return $this;
    }

    /**
     * Remove providerUserTypes
     *
     * @param <variableType$providerUserTypes
     */
    public function removeProviderUserType(\HealthCareAbroad\ProviderBundle\Entity\ProviderUserType $providerUserTypes)
    {
        $this->providerUserTypes->removeElement($providerUserTypes);
    }

    /**
     * Get providerUserTypes
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getProviderUserTypes()
    {
        return $this->providerUserTypes;
    }

    /**
     * Add providerUserType
     *
     * @param HealthCareAbroad\ProviderBundle\Entity\ProviderUserTypes $providerUserType
     * @return ProviderUserRole
     */
    public function addProviderUserType(\HealthCareAbroad\ProviderBundle\Entity\ProviderUserTypes $providerUserType)
    {
        $this->providerUserType[] = $providerUserType;
        return $this;
    }

    /**
     * Remove providerUserType
     *
     * @param <variableType$providerUserType
     */
    public function removeProviderUserType(\HealthCareAbroad\ProviderBundle\Entity\ProviderUserTypes $providerUserType)
    {
        $this->providerUserType->removeElement($providerUserType);
    }
}