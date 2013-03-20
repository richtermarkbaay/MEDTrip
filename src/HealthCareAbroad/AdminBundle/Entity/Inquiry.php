<?php

namespace HealthCareAbroad\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HealthCareAbroad\AdminBundle\Entity\Inquiry
 */
class Inquiry
{
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;
    
    
    /**
     * @var integer $id
     */
    private $id;

    /**
     * @var string $firstName
     */
    private $firstName;

    /**
     * @var string $lastName
     */
    private $lastName;

    /**
     * @var string $email
     */
    private $email;

    /**
     * @var string $contactNumber
     */
    private $contactNumber;

    /**
     * @var text $message
     */
    private $message;

    /**
     * @var datetime $dateCreated
     */
    private $dateCreated;

    /**
     * @var string $remoteAddress
     */
    private $remoteAddress;

    /**
     * @var string $httpUseAgent
     */
    private $httpUseAgent;
    
    /**
     * @var smallint $status
     */
    private $status;

    /**
     * @var HealthCareAbroad\AdminBundle\Entity\InquirySubject
     */
    private $inquirySubject;

    /**
     * @var string $clinicName
     */
    private $clinicName;
    
    /**
     * @var HealthCareAbroad\HelperBundle\Entity\Country
     */
    private $country;

    /**
     * @var HealthCareAbroad\HelperBundle\Entity\City
     */
    private $city;


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
     * Set firstName
     *
     * @param string $firstName
     * @return Inquiry
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
        return $this;
    }

    /**
     * Get firstName
     *
     * @return string 
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     * @return Inquiry
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
        return $this;
    }

    /**
     * Get lastName
     *
     * @return string 
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return Inquiry
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set contactNumber
     *
     * @param string $contactNumber
     * @return Inquiry
     */
    public function setContactNumber($contactNumber)
    {
        $this->contactNumber = $contactNumber;
        return $this;
    }

    /**
     * Get contactNumber
     *
     * @return string 
     */
    public function getContactNumber()
    {
        return $this->contactNumber;
    }

    /**
     * Set clinicName
     *
     * @param string $clinicName
     * @return Inquiry
     */
    public function setClinicName($clinicName)
    {
        $this->clinicName = $clinicName;
        return $this;
    }
    
    /**
     * Get clinicName
     *
     * @return string
     */
    public function getClinicName()
    {
        return $this->clinicName;
    }
    
    /**
     * Set message
     *
     * @param text $message
     * @return Inquiry
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
     * @return Inquiry
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
     * Set remoteAddress
     *
     * @param string $remoteAddress
     * @return Inquiry
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
     * @return Inquiry
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
     * Set status
     *
     * @param smallint $status
     * @return Inquiry
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
     * Set inquirySubject
     *
     * @param HealthCareAbroad\AdminBundle\Entity\InquirySubject $inquirySubject
     * @return Inquiry
     */
    public function setInquirySubject(\HealthCareAbroad\AdminBundle\Entity\InquirySubject $inquirySubject = null)
    {
        $this->inquirySubject = $inquirySubject;
        return $this;
    }

    /**
     * Get inquirySubject
     *
     * @return HealthCareAbroad\AdminBundle\Entity\InquirySubject 
     */
    public function getInquirySubject()
    {
        return $this->inquirySubject;
    }

    /**
     * Set country
     *
     * @param HealthCareAbroad\HelperBundle\Entity\Country $country
     * @return Inquiry
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
     * Set city
     *
     * @param HealthCareAbroad\HelperBundle\Entity\City $city
     * @return Inquiry
     */
    public function setCity(\HealthCareAbroad\HelperBundle\Entity\City $city = null)
    {
        $this->city = $city;
        return $this;
    }

    /**
     * Get city
     *
     * @return HealthCareAbroad\HelperBundle\Entity\City 
     */
    public function getCity()
    {
        return $this->city;
    }
}