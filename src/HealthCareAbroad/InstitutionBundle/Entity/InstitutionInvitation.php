<?php

namespace HealthCareAbroad\InstitutionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HealthCareAbroad\InstitutionBundle\Entity\InstitutionInvitation
 */
class InstitutionInvitation
{
	const STATUS_PENDING_SENDING = 0;
	const STATUS_SENT = 1;
	const STATUS_ACCEPTED = 2;
    /**
     * @var integer $id
     */
    private $id;

    /**
     * @var string $email
     */
    private $email;

    /**
     * @var text $message
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
     * @var smallint $status
     */
    private $status;

    /**
     * @var HealthCareAbroad\HelperBundle\Entity\InvitationToken
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
     * @return InstitutionInvitation
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
     * @param text $message
     * @return InstitutionInvitation
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
     * Set name
     *
     * @param string $name
     * @return InstitutionInvitation
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
     * @return InstitutionInvitation
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
     * @param smallint $status
     * @return InstitutionInvitation
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
     * Set invitationToken
     *
     * @param HealthCareAbroad\HelperBundle\Entity\InvitationToken $invitationToken
     * @return InstitutionInvitation
     */
    public function setInvitationToken(\HealthCareAbroad\HelperBundle\Entity\InvitationToken $invitationToken = null)
    {
        $this->invitationToken = $invitationToken;
        return $this;
    }

    /**
     * Get invitationToken
     *
     * @return HealthCareAbroad\HelperBundle\Entity\InvitationToken 
     */
    public function getInvitationToken()
    {
        return $this->invitationToken;
    }
}