<?php

namespace HealthCareAbroad\HelperBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HealthCareAbroad\HelperBundle\Entity\CommandScriptLog
 */
class CommandScriptLog
{
    const STATUS_START = 1;
    const STATUS_COMPLETED = 2;
    
    const MAX_ATTEMPT = 5;
    
    /**
     * @var integer $id
     */
    private $id;

    /**
     * @var string $scriptName
     */
    private $scriptName;

    /**
     * @var string $description
     */
    private $description;

    /**
     * @var datetime $lastDateStart
     */
    private $lastDateStart;

    /**
     * @var datetime $lastDateCompleted
     */
    private $lastDateCompleted;

    /**
     * @var smallint $attempts
     */
    private $attempts;

    /**
     * @var smallint $status
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
     * Set scriptName
     *
     * @param string $scriptName
     * @return CommandScriptLog
     */
    public function setScriptName($scriptName)
    {
        $this->scriptName = $scriptName;
        return $this;
    }

    /**
     * Get scriptName
     *
     * @return string 
     */
    public function getScriptName()
    {
        return $this->scriptName;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return CommandScriptLog
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set lastDateStart
     *
     * @param datetime $lastDateStart
     * @return CommandScriptLog
     */
    public function setLastDateStart($lastDateStart)
    {
        $this->lastDateStart = $lastDateStart;
        return $this;
    }

    /**
     * Get lastDateStart
     *
     * @return datetime 
     */
    public function getLastDateStart()
    {
        return $this->lastDateStart;
    }

    /**
     * Set lastDateCompleted
     *
     * @param datetime $lastDateCompleted
     * @return CommandScriptLog
     */
    public function setLastDateCompleted($lastDateCompleted)
    {
        $this->lastDateCompleted = $lastDateCompleted;
        return $this;
    }

    /**
     * Get lastDateCompleted
     *
     * @return datetime 
     */
    public function getLastDateCompleted()
    {
        return $this->lastDateCompleted;
    }

    /**
     * Set attempts
     *
     * @param smallint $attempts
     * @return CommandScriptLog
     */
    public function setAttempts($attempts)
    {
        $this->attempts = $attempts;
        return $this;
    }

    /**
     * Get attempts
     *
     * @return smallint 
     */
    public function getAttempts()
    {
        return $this->attempts;
    }

    /**
     * Set status
     *
     * @param smallint $status
     * @return CommandScriptLog
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