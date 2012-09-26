<?php
namespace HealthCareAbroad\MailerBundle\Entity;

class MailQueue
{
    /**
     * @var bigint $id
     */
    private $id;

    /**
     * @var text $messageData
     */
    private $messageData;

    /**
     * @var datetime $sendAt
     */
    private $sendAt;

    /**
     * @var datetime $createdAt
     */
    private $createdAt;

    /**
     * @var integer $failedAttempts
     */
    private $failedAttempts;

    /**
     * @var smallint $status
     */
    private $status;


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
     * Set messageData
     *
     * @param text $messageData
     * @return MailQueue
     */
    public function setMessageData($messageData)
    {
        $this->messageData = $messageData;
        return $this;
    }

    /**
     * Get messageData
     *
     * @return text 
     */
    public function getMessageData()
    {
        return $this->messageData;
    }

    /**
     * Set sendAt
     *
     * @param datetime $sendAt
     * @return MailQueue
     */
    public function setSendAt($sendAt)
    {
        $this->sendAt = $sendAt;
        return $this;
    }

    /**
     * Get sendAt
     *
     * @return datetime 
     */
    public function getSendAt()
    {
        return $this->sendAt;
    }

    /**
     * Set createdAt
     *
     * @param datetime $createdAt
     * @return MailQueue
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * Get createdAt
     *
     * @return datetime 
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set failedAttempts
     *
     * @param integer $failedAttempts
     * @return MailQueue
     */
    public function setFailedAttempts($failedAttempts=0)
    {
        $this->failedAttempts = $failedAttempts;
        return $this;
    }

    /**
     * Get failedAttempts
     *
     * @return integer 
     */
    public function getFailedAttempts()
    {
        return $this->failedAttempts;
    }

    /**
     * Set status
     *
     * @param smallint $status
     * @return MailQueue
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
}