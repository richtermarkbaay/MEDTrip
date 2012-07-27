<?php

namespace HealthCareAbroad\MedicalProcedureBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HealthCareAbroad\MedicalProcedureBundle\Entity\MedicalTermSuggestionDetail
 */
class MedicalTermSuggestionDetail
{
    /**
     * @var integer $id
     */
    private $id;

    /**
     * @var smallint $type
     */
    private $type;

    /**
     * @var string $name
     */
    private $name;

    /**
     * @var text $description
     */
    private $description;

    /**
     * @var smallint $status
     */
    private $status;

    /**
     * @var HealthCareAbroad\MedicalProcedureBundle\Entity\MedicalTermSuggestion
     */
    private $medicalTermSuggestion;


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
     * Set type
     *
     * @param smallint $type
     * @return MedicalTermSuggestionDetail
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Get type
     *
     * @return smallint 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return MedicalTermSuggestionDetail
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
     * @param text $description
     * @return MedicalTermSuggestionDetail
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Get description
     *
     * @return text 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set status
     *
     * @param smallint $status
     * @return MedicalTermSuggestionDetail
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
     * Set medicalTermSuggestion
     *
     * @param HealthCareAbroad\MedicalProcedureBundle\Entity\MedicalTermSuggestion $medicalTermSuggestion
     * @return MedicalTermSuggestionDetail
     */
    public function setMedicalTermSuggestion(\HealthCareAbroad\MedicalProcedureBundle\Entity\MedicalTermSuggestion $medicalTermSuggestion = null)
    {
        $this->medicalTermSuggestion = $medicalTermSuggestion;
        return $this;
    }

    /**
     * Get medicalTermSuggestion
     *
     * @return HealthCareAbroad\MedicalProcedureBundle\Entity\MedicalTermSuggestion 
     */
    public function getMedicalTermSuggestion()
    {
        return $this->medicalTermSuggestion;
    }
}