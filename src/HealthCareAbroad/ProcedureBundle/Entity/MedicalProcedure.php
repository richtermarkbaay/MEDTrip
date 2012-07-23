<?php

namespace HealthCareAbroad\ProcedureBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HealthCareAbroad\ProcedureBundle\Entity\MedicalProcedure
 */
class MedicalProcedure
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
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $tags;

    public function __construct()
    {
        $this->tags = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return MedicalProcedure
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
     * @return MedicalProcedure
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
     * Add tags
     *
     * @param HealthCareAbroad\HelperBundle\Entity\Tag $tags
     * @return MedicalProcedure
     */
    public function addTag(\HealthCareAbroad\HelperBundle\Entity\Tag $tags)
    {
    	if(!$this->tags->contains($tags)) {
        	$this->tags[] = $tags;
    	}
        return $this;
    }

    /**
     * Remove tags
     *
     * @param <variableType$tags
     */
    public function removeTag(\HealthCareAbroad\HelperBundle\Entity\Tag $tags)
    {
        $this->tags->removeElement($tags);
    }

    /**
     * Get tags
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getTags()
    {
        return $this->tags;
    }
}