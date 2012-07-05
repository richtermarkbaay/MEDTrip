<?php

namespace HealthCareAbroad\HelperBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HealthCareAbroad\HelperBundle\Entity\InvitationToken
 */
class InvitationToken
{
    /**
     * @var integer $id
     */
    private $id;

    /**
     * @var string $token
     */
    private $token;

    /**
     * @var datetime $dateCreated
     */
    private $dateCreated;

    /**
     * @var datetime $expirationDate
     */
    private $expirationDate;

    /**
     * @var boolean $status
     */
    private $status;


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
     * Set token
     *
     * @param string $token
     * @return InvitationToken
     */
    public function setToken($token)
    {
        $this->token = $token;
        return $this;
    }

    /**
     * Get token
     *
     * @return string 
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set dateCreated
     *
     * @param datetime $dateCreated
     * @return InvitationToken
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
     * Set expirationDate
     *
     * @param datetime $expirationDate
     * @return InvitationToken
     */
    public function setExpirationDate($expirationDate)
    {
        $this->expirationDate = $expirationDate;
        return $this;
    }

    /**
     * Get expirationDate
     *
     * @return datetime 
     */
    public function getExpirationDate()
    {
        return $this->expirationDate;
    }

    /**
     * Set status
     *
     * @param boolean $status
     * @return InvitationToken
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
}