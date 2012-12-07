<?php

namespace HealthCareAbroad\HelperBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HealthCareAbroad\HelperBundle\Entity\DataType
 */
class DataType
{
    /**
     * @var smallint $id
     */
    private $id;

    /**
     * @var string $columnType
     */
    private $columnType;
    
    /**
     * @var string $formField
     */
    private $formField;


    /**
     * Get id
     *
     * @return smallint 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set columnType
     *
     * @param string $columnType
     * @return DataType
     */
    public function setColumnType($columnType)
    {
        $this->columnType = $columnType;
        return $this;
    }

    /**
     * Get columnType
     *
     * @return string 
     */
    public function getColumnType()
    {
        return $this->columnType;
    }

    /**
     * Set formField
     *
     * @param string $formField
     * @return DataType
     */
    public function setFormField($formField)
    {
        $this->formField = $formField;
        return $this;
    }

    /**
     * Get formField
     *
     * @return string 
     */
    public function getFormField()
    {
        return $this->formField;
    }
}