<?php

namespace HealthCareAbroad\InstitutionBundle\Entity;

class InstitutionInquiry
{
    const STATUS_SAVE = 1;
    /**
     * @var bigint $id
     */
    private $id;

    /**
     * @var string $inquirer_name
     */
    private $inquirer_name;

    /**
     * @var string $inquirer_email
     */
    private $inquirer_email;

    /**
     * @var HealthCareAbroad\HelperBundle\Entity\Country
     */
    private $country;
    
    /**
     * @var text $message
     */
    private $message;

    /**
     * @var datetime $dateCreated
     */
    private $dateCreated;

    /**
     * @var tinyint $status
     */
    private $status;

    /**
     * @var HealthCareAbroad\InstitutionBundle\Entity\Institution
     */
    private $institution;

    /**
     * @var HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter
     */
    private $institutionMedicalCenter;


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
     * Set inquirer_name
     *
     * @param string $inquirerName
     * @return InstitutionInquiry
     */
    public function setInquirerName($inquirerName)
    {
        $this->inquirer_name = $inquirerName;
        return $this;
    }

    /**
     * Get inquirer_name
     *
     * @return string 
     */
    public function getInquirerName()
    {
        return $this->inquirer_name;
    }

    /**
     * Set inquirer_email
     *
     * @param string $inquirerEmail
     * @return InstitutionInquiry
     */
    public function setInquirerEmail($inquirerEmail)
    {
        $this->inquirer_email = $inquirerEmail;
        return $this;
    }

    /**
     * Get inquirer_email
     *
     * @return string 
     */
    public function getInquirerEmail()
    {
        return $this->inquirer_email;
    }

    /**
     * Set country
     *
     * @param HealthCareAbroad\HelperBundle\Entity\Country $country
     * @return InstitutionInquiry
     */
    public function setCountry(\HealthCareAbroad\HelperBundle\Entity\Country $country = null)
    {
        $this->country = $country;
        return $this;
    }
    
    /**
     * Get country
     *
     * @return HealthCareAbroad\HelperBundle\Entity\Country
     */
    public function getCountry()
    {
        return $this->country;
    }
    
    /**
     * Set message
     *
     * @param text $message
     * @return InstitutionInquiry
     */
    public function setMessage($message)
    {
        $this->message = $message;
        return $this;
    }

    /**
     * Get message
     *
     * @return text 
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set dateCreated
     *
     * @param datetime $dateCreated
     * @return InstitutionInquiry
     */
    public function setDateCreated($dateCreated)
    {
        $this->dateCreated = $dateCreated;
        return $this;
    }

    /**
     * Get dateCreated
     *
     * @return datetime 
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    /**
     * Set status
     *
     * @param tinyint $status
     * @return InstitutionInquiry
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * Get status
     *
     * @return tinyint 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set institution
     *
     * @param HealthCareAbroad\InstitutionBundle\Entity\Institution $institution
     * @return InstitutionInquiry
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
     * @return InstitutionInquiry
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
}