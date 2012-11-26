<?php

namespace HealthCareAbroad\FrontendBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HealthCareAbroad\FrontendBundle\Entity\NewsletterSubscriber
 */
class NewsletterSubscriber
{
    /**
     * @var integer $id
     */
    private $id;

    /**
     * @var string $email
     */
    private $email;

    /**
     * @var string $ip_address
     */
    private $ip_address;

    /**
     * @var string $ip_address
     */
    private $date_created;

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
     * Set email
     *
     * @param string $email
     * @return NewsletterSubscriber
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
     * Set ip_address
     *
     * @param string $ipAddress
     * @return NewsletterSubscriber
     */
    public function setIpAddress($ipAddress)
    {
        $this->ip_address = $ipAddress;
        return $this;
    }

    /**
     * Get ip_address
     *
     * @return string 
     */
    public function getIpAddress()
    {
        return $this->ip_address;
    }
    
    /**
     * Set date_created
     *
     * @param string $dateCreated
     * @return NewsletterSubscriber
     */
    public function setDateCreated($dateCreated)
    {
    	$this->dateCreated = $dateCreated;
    	return $this;
    }
    
    /**
     * Get dateCreated
     *
     * @return string
     */
    public function getDateCreated()
    {
    	return $this->dateCreated;
    }
}