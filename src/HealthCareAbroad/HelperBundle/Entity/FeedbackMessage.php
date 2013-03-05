<?php

namespace HealthCareAbroad\HelperBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HealthCareAbroad\HelperBundle\Entity\FeedbackMessage
 */
class FeedbackMessage
{
    /**
     * @var integer $id
     */
    private $id;

    /**
     * @var string $name
     */
    private $name;
    
    /**
     * @var string $emailAddress
     */
    private $emailAddress;

    /**
     * @var HealthCareAbroad\HelperBundle\Entity\Country
     */
    private $country;
    
    /**
     * @var text $message
     */
    private $message;

    /**
     * @var string $remoteAddress
     */
    private $remoteAddress;

    /**
     * @var string $httpUseAgent
     */
    private $httpUseAgent;

    /**
     * @var datetime $dateCreated
     */
    private $dateCreated;


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
     * Set name
     *
     * @param string $name
     * @return FeedbackMessage
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
     * Set emailAddress
     *
     * @param string $emailAddress
     * @return FeedbackMessage
     */
    public function setEmailAddress($emailAddress)
    {
        $this->emailAddress = $emailAddress;
        return $this;
    }
    
    /**
     * Get emailAddress
     *
     * @return string
     */
    public function getEmailAddress()
    {
        return $this->emailAddress;
    }

    /**
     * Set country
     *
     * @param HealthCareAbroad\HelperBundle\Entity\Country $country
     * @return FeedbackMessage
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
     * @return FeedbackMessage
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
     * Set remoteAddress
     *
     * @param string $remoteAddress
     * @return FeedbackMessage
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
     * @return FeedbackMessage
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
     * @param datetime $dateCreated
     * @return FeedbackMessage
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
}