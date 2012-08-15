<?php

namespace HealthCareAbroad\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HealthCareAbroad\AdminBundle\Entity\Inquiry
 */
class Inquiry
{
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
     * @var text $message
     */
    private $message;

    /**
     * @var datetime $dateCreated
     */
    private $dateCreated;

    /**
     * @var smallint $status
     */
    private $status;

    /**
     * @var HealthCareAbroad\AdminBundle\Entity\InquirySubject
     */
    private $inquirySubject;


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
}