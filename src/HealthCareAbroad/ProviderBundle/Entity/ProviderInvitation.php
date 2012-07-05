<?php

namespace HealthCareAbroad\ProviderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HealthCareAbroad\ProviderBundle\Entity\ProviderInvitation
 */
class ProviderInvitation
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
     * @var string $message
     */
    private $message;

    /**
     * @var string $name
     */
    private $name;

    /**
     * @var datetime $dateCreated
     */
    private $dateCreated;

    /**
     * @var boolean $status
     */
    private $status;

    /**
     * @var HealthCareAbroad\ProviderBundle\Entity\InvitationTokens
     */
    private $invitationToken;


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
     * @return ProviderInvitation
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
     * Set message
     *
     * @param string $message
     * @return ProviderInvitation
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
     * Set name
     *
     * @param string $name
     * @return ProviderInvitation
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
     * Set dateCreated
     *
     * @param datetime $dateCreated
     * @return ProviderInvitation
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
     * @param boolean $status
     * @return ProviderInvitation
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * Get status
     *
     * @return boolean 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set invitationToken
     *
     * @param HealthCareAbroad\ProviderBundle\Entity\InvitationTokens $invitationToken
     * @return ProviderInvitation
     */
    public function setInvitationToken(\HealthCareAbroad\ProviderBundle\Entity\InvitationTokens $invitationToken = null)
    {
        $this->invitationToken = $invitationToken;
        return $this;
    }

    /**
     * Get invitationToken
     *
     * @return HealthCareAbroad\ProviderBundle\Entity\InvitationTokens 
     */
    public function getInvitationToken()
    {
        return $this->invitationToken;
    }
}