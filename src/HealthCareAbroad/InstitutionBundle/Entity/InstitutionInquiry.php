<?php

namespace HealthCareAbroad\InstitutionBundle\Entity;

class InstitutionInquiry
{
    const STATUS_DELETED = 0;
    
    const STATUS_UNAPPROVED = 0;
    
    const STATUS_UNREAD = 1;
    
    const STATUS_READ = 2;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $inquirerName;

    /**
     * @var string
     */
    private $inquirerEmail;

    /**
     * @var string
     */
    private $message;

    /**
     * @var string
     */
    private $remoteAddress;

    /**
     * @var string
     */
    private $httpUseAgent;

    /**
     * @var \DateTime
     */
    private $dateCreated;

    /**
     * @var integer
     */
    private $status;

    /**
     * @var \HealthCareAbroad\InstitutionBundle\Entity\Institution
     */
    private $institution;

    /**
     * @var \HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter
     */
    private $institutionMedicalCenter;

    /**
     * @var \HealthCareAbroad\HelperBundle\Entity\Country
     */
    private $country;


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
     * Set inquirerName
     *
     * @param string $inquirerName
     * @return InstitutionInquiry
     */
    public function setInquirerName($inquirerName)
    {
        $this->inquirerName = $inquirerName;
    
        return $this;
    }

    /**
     * Get inquirerName
     *
     * @return string 
     */
    public function getInquirerName()
    {
        return $this->inquirerName;
    }

    /**
     * Set inquirerEmail
     *
     * @param string $inquirerEmail
     * @return InstitutionInquiry
     */
    public function setInquirerEmail($inquirerEmail)
    {
        $this->inquirerEmail = $inquirerEmail;
    
        return $this;
    }

    /**
     * Get inquirerEmail
     *
     * @return string 
     */
    public function getInquirerEmail()
    {
        return $this->inquirerEmail;
    }

    /**
     * Set message
     *
     * @param string $message
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
     * @return string 
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set remoteAddress
     *
     * @param string $remoteAddress
     * @return InstitutionInquiry
     */
    public function setRemoteAddress($remoteAddress)
    {
        $this->remoteAddress = $remoteAddress;
    
        return $this;
    }

    /**
     * Get remoteAddress
     *
     * @return string 
     */
    public function getRemoteAddress()
    {
        return $this->remoteAddress;
    }

    /**
     * Set httpUseAgent
     *
     * @param string $httpUseAgent
     * @return InstitutionInquiry
     */
    public function setHttpUseAgent($httpUseAgent)
    {
        $this->httpUseAgent = $httpUseAgent;
    
        return $this;
    }

    /**
     * Get httpUseAgent
     *
     * @return string 
     */
    public function getHttpUseAgent()
    {
        return $this->httpUseAgent;
    }

    /**
     * Set dateCreated
     *
     * @param \DateTime $dateCreated
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
     * @return \DateTime 
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    /**
     * Set status
     *
     * @param integer $status
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
     * @return integer 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set institution
     *
     * @param \HealthCareAbroad\InstitutionBundle\Entity\Institution $institution
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
     * @return \HealthCareAbroad\InstitutionBundle\Entity\Institution 
     */
    public function getInstitution()
    {
        return $this->institution;
    }

    /**
     * Set institutionMedicalCenter
     *
     * @param \HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter $institutionMedicalCenter
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
     * @return \HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter 
     */
    public function getInstitutionMedicalCenter()
    {
        return $this->institutionMedicalCenter;
    }

    /**
     * Set country
     *
     * @param \HealthCareAbroad\HelperBundle\Entity\Country $country
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
     * @return \HealthCareAbroad\HelperBundle\Entity\Country 
     */
    public function getCountry()
    {
        return $this->country;
    }
}