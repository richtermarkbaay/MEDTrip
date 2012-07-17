<?php

namespace HealthCareAbroad\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HealthCareAbroad\UserBundle\Entity\ProviderUserRole
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
     * @param HealthCareAbroad\UserBundle\Entity\ProviderUserType $providerUserTypes
     * @return ProviderUserRole
     */
    public function addProviderUserType(\HealthCareAbroad\UserBundle\Entity\ProviderUserType $providerUserTypes)
    {
        $this->providerUserTypes[] = $providerUserTypes;
        return $this;
    }

    /**
     * Remove providerUserTypes
     *
     * @param <variableType$providerUserTypes
     */
    public function removeProviderUserType(\HealthCareAbroad\UserBundle\Entity\ProviderUserType $providerUserTypes)
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
}