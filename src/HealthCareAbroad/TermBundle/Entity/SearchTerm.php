<?php

namespace HealthCareAbroad\TermBundle\Entity;

class SearchTerm
{
    const STATUS_ACTIVE = 1;
    
    const STATUS_INACTIVE = 0;
    /**
     * @var bigint $id
     */
    private $id;

    /**
     * @var bigint $termDocumentId
     */
    private $termDocumentId;

    /**
     * @var bigint $documentId
     */
    private $documentId;

    /**
     * @var integer $type
     */
    private $type;

    /**
     * @var integer $status
     */
    private $status;

    /**
     * @var HealthCareAbroad\TermBundle\Entity\Term
     */
    private $term;

    /**
     * @var HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter
     */
    private $institutionMedicalCenter;

    /**
     * @var HealthCareAbroad\InstitutionBundle\Entity\Institution
     */
    private $institution;


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
     * Set termDocumentId
     *
     * @param bigint $termDocumentId
     * @return SearchTerm
     */
    public function setTermDocumentId($termDocumentId)
    {
        $this->termDocumentId = $termDocumentId;
        return $this;
    }

    /**
     * Get termDocumentId
     *
     * @return bigint 
     */
    public function getTermDocumentId()
    {
        return $this->termDocumentId;
    }

    /**
     * Set documentId
     *
     * @param bigint $documentId
     * @return SearchTerm
     */
    public function setDocumentId($documentId)
    {
        $this->documentId = $documentId;
        return $this;
    }

    /**
     * Get documentId
     *
     * @return bigint 
     */
    public function getDocumentId()
    {
        return $this->documentId;
    }

    /**
     * Set type
     *
     * @param integer $type
     * @return SearchTerm
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
     * Set status
     *
     * @param integer $status
     * @return SearchTerm
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * Get status
     *
     * @return integer 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set term
     *
     * @param HealthCareAbroad\TermBundle\Entity\Term $term
     * @return SearchTerm
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

    /**
     * Set institutionMedicalCenter
     *
     * @param HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter $institutionMedicalCenter
     * @return SearchTerm
     */
    public function setInstitutionMedicalCenter(\HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter $institutionMedicalCenter = null)
    {
        $this->institutionMedicalCenter = $institutionMedicalCenter;
        return $this;
    }

    /**
     * Get institutionMedicalCenter
     *
     * @return HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter 
     */
    public function getInstitutionMedicalCenter()
    {
        return $this->institutionMedicalCenter;
    }

    /**
     * Set institution
     *
     * @param HealthCareAbroad\InstitutionBundle\Entity\Institution $institution
     * @return SearchTerm
     */
    public function setInstitution(\HealthCareAbroad\InstitutionBundle\Entity\Institution $institution = null)
    {
        $this->institution = $institution;
        return $this;
    }

    /**
     * Get institution
     *
     * @return HealthCareAbroad\InstitutionBundle\Entity\Institution 
     */
    public function getInstitution()
    {
        return $this->institution;
    }
}