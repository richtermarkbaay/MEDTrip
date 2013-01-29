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
     * @var string $term
     */
    private $term;


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
     * Set term
     *
     * @param string $term
     * @return Terms
     */
    public function setTerm($term)
    {
        $this->term = $term;
        return $this;
    }

    /**
     * Get term
     *
     * @return string
     */
    public function getTerm()
    {
        return $this->term;
    }
    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $termDocuments;

    public function __construct()
    {
        $this->termDocuments = new \Doctrine\Common\Collections\ArrayCollection();
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
}