<?php

namespace HealthCareAbroad\HelperBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HealthCareAbroad\HelperBundle\Entity\AwardingBodies
 */
class AwardingBody
{
	const STATUS_ACTIVE = 1;
	
	const STATUS_INACTIVE = 0;
    /**
     * @var integer $id
     */
    private $id;

    /**
     * @var string $name
     */
    private $name;

    /**
     * @var string $details
     */
    private $details;

    /**
     * @var string $website
     */
    private $website;

    /**
     * @var smallint $status
     */
    private $status;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $globalAwards;

    public function __construct()
    {
        $this->globalAwards = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return AwardingBody
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
     * Set details
     *
     * @param string $details
     * @return AwardingBody
     */
    public function setDetails($details)
    {
        $this->details = $details;
        return $this;
    }

    /**
     * Get details
     *
     * @return string 
     */
    public function getDetails()
    {
        return $this->details;
    }

    /**
     * Set website
     *
     * @param string $website
     * @return AwardingBody
     */
    public function setWebsite($website)
    {
        $this->website = $website;
        return $this;
    }

    /**
     * Get website
     *
     * @return string 
     */
    public function getWebsite()
    {
        return $this->website;
    }

    /**
     * Set status
     *
     * @param smallint $status
     * @return AwardingBody
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * Get status
     *
     * @return smallint 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Add globalAwards
     *
     * @param HealthCareAbroad\HelperBundle\Entity\GlobalAward $globalAwards
     * @return AwardingBody
     */
    public function addGlobalAward(\HealthCareAbroad\HelperBundle\Entity\GlobalAward $globalAwards)
    {
        $this->globalAwards[] = $globalAwards;
        return $this;
    }

    /**
     * Remove globalAwards
     *
     * @param HealthCareAbroad\HelperBundle\Entity\GlobalAward $globalAwards
     */
    public function removeGlobalAward(\HealthCareAbroad\HelperBundle\Entity\GlobalAward $globalAwards)
    {
        $this->globalAwards->removeElement($globalAwards);
    }

    /**
     * Get globalAwards
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getGlobalAwards()
    {
        return $this->globalAwards;
    }
}