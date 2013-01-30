<?php

namespace HealthCareAbroad\TermBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HealthCareAbroad\TermBundle\Entity\TermDocument
 */
class TermDocument
{
    const TYPE_SPECIALIZATION = 1;
    const TYPE_SUBSPECIALIZATION = 2;
    const TYPE_TREATMENT = 3;

    /**
     * @var bigint $id
     */
    private $id;

    /**
     * @var integer $documentId
     */
    private $documentId;

    /**
     * @var integer $type
     */
    private $type;

    /**
     * @var HealthCareAbroad\TermBundle\Entity\Term
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
     * Set documentId
     *
     * @param integer $documentId
     * @return TermDocument
     */
    public function setDocumentId($documentId)
    {
        $this->documentId = $documentId;
        return $this;
    }

    /**
     * Get documentId
     *
     * @return integer
     */
    public function getDocumentId()
    {
        return $this->documentId;
    }

    /**
     * Set type
     *
     * @param integer $type
     * @return TermDocument
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Get type
     *
     * @return integer
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set term
     *
     * @param HealthCareAbroad\TermBundle\Entity\Term $term
     * @return TermDocument
     */
    public function setTerm(\HealthCareAbroad\TermBundle\Entity\Term $term = null)
    {
        $this->term = $term;
        return $this;
    }

    /**
     * Get term
     *
     * @return HealthCareAbroad\TermBundle\Entity\Term
     */
    public function getTerm()
    {
        return $this->term;
    }
}