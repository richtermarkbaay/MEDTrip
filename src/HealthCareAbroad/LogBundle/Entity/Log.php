<?php
namespace HealthCareAbroad\LogBundle\Entity;

class Log
{
    /**
     * @var bigint $id
     */
    private $id;

    /**
     * @var bigint $accountId
     */
    private $accountId;

    /**
     * @var integer $applicationContext
     */
    private $applicationContext;

    /**
     * @var string $action
     */
    private $action;

    /**
     * @var bigint $objectId
     */
    private $objectId;

    /**
     * @var datetime $dateCreated
     */
    private $dateCreated;

    /**
     * @var HealthCareAbroad\LogBundle\Entity\LogClass
     */
    private $logClass;


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
     * Set accountId
     *
     * @param bigint $accountId
     * @return Log
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
     * Set applicationContext
     *
     * @param integer $applicationContext
     * @return Log
     */
    public function setApplicationContext($applicationContext)
    {
        $this->applicationContext = $applicationContext;
        return $this;
    }

    /**
     * Get applicationContext
     *
     * @return integer 
     */
    public function getApplicationContext()
    {
        return $this->applicationContext;
    }

    /**
     * Set action
     *
     * @param string $action
     * @return Log
     */
    public function setAction($action)
    {
        $this->action = $action;
        return $this;
    }

    /**
     * Get action
     *
     * @return string 
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Set objectId
     *
     * @param bigint $objectId
     * @return Log
     */
    public function setObjectId($objectId)
    {
        $this->objectId = $objectId;
        return $this;
    }

    /**
     * Get objectId
     *
     * @return bigint 
     */
    public function getObjectId()
    {
        return $this->objectId;
    }

    /**
     * Set dateCreated
     *
     * @param datetime $dateCreated
     * @return Log
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
     * Set logClass
     *
     * @param HealthCareAbroad\LogBundle\Entity\LogClass $logClass
     * @return Log
     */
    public function setLogClass(\HealthCareAbroad\LogBundle\Entity\LogClass $logClass = null)
    {
        $this->logClass = $logClass;
        return $this;
    }

    /**
     * Get logClass
     *
     * @return HealthCareAbroad\LogBundle\Entity\LogClass 
     */
    public function getLogClass()
    {
        return $this->logClass;
    }
}