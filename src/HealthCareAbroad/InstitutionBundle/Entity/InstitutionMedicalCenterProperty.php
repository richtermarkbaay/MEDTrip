<?php
namespace HealthCareAbroad\InstitutionBundle\Entity;
class InstitutionMedicalCenterProperty
{
    /**
     * @var bigint $id
     */
    private $id;

    /**
     * @var text $value
     */
    private $value;

    /**
     * @var HealthCareAbroad\InstitutionBundle\Entity\Institution
     */
    private $institution;

    /**
     * @var HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter
     */
    private $institutionMedicalCenter;
    
    /**
     * @var HealthCareAbroad\InstitutionBundle\Entity\InstitutionPropertyType
     */
    private $institutionPropertyType;


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
     * Set value
     *
     * @param text $value
     * @return InstitutionProperty
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * Get value
     *
     * @return text 
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set institution
     *
     * @param HealthCareAbroad\InstitutionBundle\Entity\Institution $institution
     * @return InstitutionProperty
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

    /**
     * Set institutionMedicalCenter
     *
     * @param HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter $institutionMedicalCenter
     * @return InstitutionMedicalCenterProperty
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
     * Set institutionPropertyType
     *
     * @param HealthCareAbroad\InstitutionBundle\Entity\InstitutionPropertyType $institutionPropertyType
     * @return InstitutionProperty
     */
    public function setInstitutionPropertyType(\HealthCareAbroad\InstitutionBundle\Entity\InstitutionPropertyType $institutionPropertyType = null)
    {
        $this->institutionPropertyType = $institutionPropertyType;
        return $this;
    }

    /**
     * Get institutionPropertyType
     *
     * @return HealthCareAbroad\InstitutionBundle\Entity\InstitutionPropertyType 
     */
    public function getInstitutionPropertyType()
    {
        return $this->institutionPropertyType;
    }
    /**
     * @var text $extraValue
     */
    private $extraValue;


    /**
     * Set extraValue
     *
     * @param text $extraValue
     * @return InstitutionMedicalCenterProperty
     */
    public function setExtraValue($extraValue)
    {
        $this->extraValue = $extraValue;
        return $this;
    }

    /**
     * Get extraValue
     *
     * @return text 
     */
    public function getExtraValue()
    {
        return $this->extraValue;
    }
    
    /* ----- custom methods here ----- */
    /**
     * @var Mixed object representation of the value
     */
    private $valueObject;
    
    public function setValueObject($v)
    {
        $this->valueObject = $v;
        return $this;
    }
    
    public function getValueObject()
    {
        return $this->valueObject;
    }
    
    /* ----- end custom methods ----- */
    
}