<?php
namespace HealthCareAbroad\LogBundle\Entity;

class VersionEntry
{
    
    /**
     * @var bigint $id
     */
    private $id;

    /**
     * @var string $action
     */
    private $action;

    /**
     * @var datetime $loggedAt
     */
    private $loggedAt;

    /**
     * @var bigint $objectId
     */
    private $objectId;

    /**
     * @var string $objectClass
     */
    private $objectClass;

    /**
     * @var integer $version
     */
    private $version;

    /**
     * @var string $username
     */
    private $username;

    /**
     * @var array $data
     */
    private $data;


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
     * Set action
     *
     * @param string $action
     * @return VersionEntry
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
     * Set loggedAt
     *
     * @param datetime $loggedAt
     * @return VersionEntry
     */
    public function setLoggedAt($loggedAt=null)
    {
        $this->loggedAt = $loggedAt;
        return $this;
    }

    /**
     * Get loggedAt
     *
     * @return datetime 
     */
    public function getLoggedAt()
    {
        return $this->loggedAt;
    }

    /**
     * Set objectId
     *
     * @param bigint $objectId
     * @return VersionEntry
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
     * Set objectClass
     *
     * @param string $objectClass
     * @return VersionEntry
     */
    public function setObjectClass($objectClass)
    {
        $this->objectClass = $objectClass;
        return $this;
    }

    /**
     * Get objectClass
     *
     * @return string 
     */
    public function getObjectClass()
    {
        return $this->objectClass;
    }

    /**
     * Set version
     *
     * @param integer $version
     * @return VersionEntry
     */
    public function setVersion($version)
    {
        $this->version = $version;
        return $this;
    }

    /**
     * Get version
     *
     * @return integer 
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Set username
     *
     * @param string $username
     * @return VersionEntry
     */
    public function setUsername($username)
    {
        $this->username = $username;
        return $this;
    }

    /**
     * Get username
     *
     * @return string 
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set data
     *
     * @param array $data
     * @return VersionEntry
     */
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * Get data
     *
     * @return array 
     */
    public function getData()
    {
        return $this->data;
    }
}