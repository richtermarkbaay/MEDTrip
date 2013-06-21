<?php

namespace HealthCareAbroad\TermBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HealthCareAbroad\TermBundle\Entity\Term
 */
class Term
{
    /**
     * @var bigint $id
     */
    private $id;

    /**
     * @var string $name
     */
    private $name;

    /**
     * @var string $slug
     */
    private $slug;
    
    /**
     * @var boolean
     */
    private $internal;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $termDocuments;

    public function __construct()
    {
        $this->termDocuments = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get id
     *
     * @return bigint
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Term
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
     * Set slug
     *
     * @param string $slug
     * @return Term
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Add termDocuments
     *
     * @param HealthCareAbroad\TermBundle\Entity\TermDocument $termDocuments
     * @return Term
     */
    public function addTermDocument(\HealthCareAbroad\TermBundle\Entity\TermDocument $termDocuments)
    {
        $this->termDocuments[] = $termDocuments;
        return $this;
    }

    /**
     * Remove termDocuments
     *
     * @param HealthCareAbroad\TermBundle\Entity\TermDocument $termDocuments
     */
    public function removeTermDocument(\HealthCareAbroad\TermBundle\Entity\TermDocument $termDocuments)
    {
        $this->termDocuments->removeElement($termDocuments);
    }

    /**
     * Get termDocuments
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getTermDocuments()
    {
        return $this->termDocuments;
    }
    


    /**
     * Set internal
     *
     * @param boolean $internal
     * @return Term
     */
    public function setInternal($internal)
    {
        $this->internal = $internal;
    
        return $this;
    }

    /**
     * Get internal
     *
     * @return boolean 
     */
    public function getInternal()
    {
        return $this->internal;
    }
}