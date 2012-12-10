<?php

namespace HealthCareAbroad\HelperBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HealthCareAbroad\HelperBundle\Entity\Feedback
 */
class Feedback
{
    /**
     * @var integer $id
     */
    private $id;

    /**
     * @var bigint $accountId
     */
    private $accountId;

    /**
     * @var string $subject
     */
    private $subject;

    /**
     * @var text $message
     */
    private $message;

    /**
     * @var datetime $dateCreated
     */
    private $dateCreated;

    /**
     * @var HealthCareAbroad\InstitutionBundle\Entity\Institution
     */
    private $institution;

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
     * Set accountId
     *
     * @param bigint $accountId
     * @return Feedback
     */
    public function setAccountId($accountId)
    {
        $this->accountId = $accountId;
        return $this;
    }

    /**
     * Get accountId
     *
     * @return bigint 
     */
    public function getAccountId()
    {
        return $this->accountId;
    }

    /**
     * Set subject
     *
     * @param string $subject
     * @return Feedback
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
        return $this;
    }

    /**
     * Get subject
     *
     * @return string 
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Set message
     *
     * @param text $message
     * @return Feedback
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
     * @return Feedback
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
     * Set institution
     *
     * @param HealthCareAbroad\InstitutionBundle\Entity\Institution $institution
     * @return Feedback
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
}