<?php

namespace HealthCareAbroad\TermBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HealthCareAbroad\TermBundle\Entity\TermDocument
 */
class TermDocument
{
    /**
     * @var bigint $id
     */
    private $id;

    /**
     * @var integer $documentId
     */
    private $documentId;

    /**
     * @var boolean $type
     */
    private $type;

    /**
     * @var HealthCareAbroad\TermBundle\Entity\Terms
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
     * @return TermDocuments
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
     * @param boolean $type
     * @return TermDocuments
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Get type
     *
     * @return boolean
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set term
     *
     * @param HealthCareAbroad\TermBundle\Entity\Term $term
     * @return TermDocuments
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