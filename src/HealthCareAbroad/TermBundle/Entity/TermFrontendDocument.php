<?php
namespace HealthCareAbroad\TermBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HealthCareAbroad\TermBundle\Entity\TermFrontendDocument
 */
class TermFrontendDocument
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
     * TODO: remove this??
     *
     * @var string $elements
     */
    private $elements;

    /**
     * @var integer $termId
     */
    private $termId;

    /**
     * @var integer $specializationId
     */
    private $specializationId;

    /**
     * @var integer $subSpecializationId
     */
    private $subSpecializationId;

    /**
     * @var integer $treatmentId
     */
    private $treatmentId;

    /**
     * @var integer $countryId
     */
    private $countryId;

    /**
     * @var integer $cityId
     */
    private $cityId;

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
     * @return TermFrontendDocument
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
     * Set elements
     *
     * @param string $elements
     * @return TermFrontendDocument
     */
    public function setElements($elements)
    {
        $this->elements = $elements;
        return $this;
    }

    /**
     * Get elements
     *
     * @return integer
     */
    public function getElements()
    {
        return $this->elements;
    }

    /**
     * Set type
     *
     * @param integer $type
     * @return TermFrontendDocument
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
     * Set termId
     *
     * @param integer $termId
     * @return TermFrontendDocument
     */
    public function setTermId($termId)
    {
        $this->termId = $termId;
        return $this;
    }

    /**
     * Get termId
     *
     * @return integer
     */
    public function getTermId()
    {
        return $this->termId;
    }

    /**
     * Set specializationId
     *
     * @param integer $specializationId
     * @return TermFrontendDocument
     */
    public function setSpecializationId($specializationId)
    {
        $this->specializationId = $specializationId;
        return $this;
    }

    /**
     * Get specializationId
     *
     * @return integer
     */
    public function getSpecializationId()
    {
        return $this->specializationId;
    }

    /**
     * Set subSpecializationId
     *
     * @param integer $subSpecializationId
     * @return TermFrontendDocument
     */
    public function setSubSpecializationId($subSpecializationId)
    {
        $this->subSpecializationId = $subSpecializationId;
        return $this;
    }

    /**
     * Get subSpecializationId
     *
     * @return integer
     */
    public function getSubSpecializationId()
    {
        return $this->subSpecializationId;
    }

    /**
     * Set treatmentId
     *
     * @param integer $treatmentId
     * @return TermFrontendDocument
     */
    public function setTreatmentId($treatmentId)
    {
        $this->treatmentId = $treatmentId;
        return $this;
    }

    /**
     * Get treatmentId
     *
     * @return integer
     */
    public function getTreatmentId()
    {
        return $this->treatmentId;
    }

    /**
     * Set countryId
     *
     * @param integer $countryId
     * @return TermFrontendDocument
     */
    public function setCountryId($countryId)
    {
        $this->countryId = $countryId;
        return $this;
    }

    /**
     * Get countryId
     *
     * @return integer
     */
    public function getCountryId()
    {
        return $this->countryId;
    }

    /**
     * Set cityId
     *
     * @param integer $cityId
     * @return TermFrontendDocument
     */
    public function setCityId($cityId)
    {
        $this->cityId = $cityId;
        return $this;
    }

    /**
     * Get cityId
     *
     * @return integer
     */
    public function getCityId()
    {
        return $this->cityId;
    }
}