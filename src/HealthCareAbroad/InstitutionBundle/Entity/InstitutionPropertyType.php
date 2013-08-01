<?php
namespace HealthCareAbroad\InstitutionBundle\Entity;

class InstitutionPropertyType
{
    const STATUS_ACTIVE = 1;
    
    const STATUS_INACTIVE = 0;
    
    const TYPE_ANCILLIARY_SERVICE = 'ancilliary_service_id';
    
    const TYPE_LANGUAGE = 'language_id';
    
    const TYPE_GLOBAL_AWARD = 'global_award_id';
    
    const GLOBAL_AWARD_ID = 3;
    
    const ANCILLIARY_SERVICE_ID = 1;
    
    /**
     * @var bigint $id
     */
    private $id;

    /**
     * @var string $name
     */
    private $name;

    /**
     * @var string $label
     */
    private $label;

    /**
     * @var string $dataClass
     */
    private $dataClass;

    /**
     * @var smallint $status
     */
    private $status;

    /**
     * @var HealthCareAbroad\HelperBundle\Entity\DataType
     */
    private $dataType;
    
    /**
     * @var text $formConfiguration
     */
    private $formConfiguration;


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
     * @return InstitutionPropertyType
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
     * Set label
     *
     * @param string $label
     * @return InstitutionPropertyType
     */
    public function setLabel($label)
    {
        $this->label = $label;
        return $this;
    }

    /**
     * Get label
     *
     * @return string 
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Set dataClass
     *
     * @param string $dataClass
     * @return InstitutionPropertyType
     */
    public function setDataClass($dataClass)
    {
        $this->dataClass = $dataClass;
        return $this;
    }

    /**
     * Get dataClass
     *
     * @return string 
     */
    public function getDataClass()
    {
        return $this->dataClass;
    }

    /**
     * Set status
     *
     * @param smallint $status
     * @return InstitutionPropertyType
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
     * Set dataType
     *
     * @param HealthCareAbroad\HelperBundle\Entity\DataType $dataType
     * @return InstitutionPropertyType
     */
    public function setDataType(\HealthCareAbroad\HelperBundle\Entity\DataType $dataType = null)
    {
        $this->dataType = $dataType;
        return $this;
    }

    /**
     * Get dataType
     *
     * @return HealthCareAbroad\HelperBundle\Entity\DataType 
     */
    public function getDataType()
    {
        return $this->dataType;
    }

    /**
     * Set formConfiguration
     *
     * @param text $formConfiguration
     * @return InstitutionPropertyType
     */
    public function setFormConfiguration($formConfiguration)
    {
        $this->formConfiguration = $formConfiguration;
        return $this;
    }

    /**
     * Get formConfiguration
     *
     * @return text 
     */
    public function getFormConfiguration()
    {
        return $this->formConfiguration;
    }
}