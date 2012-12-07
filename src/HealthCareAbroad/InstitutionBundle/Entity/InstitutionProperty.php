<?php
namespace HealthCareAbroad\InstitutionBundle\Entity;
class InstitutionProperty
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
}