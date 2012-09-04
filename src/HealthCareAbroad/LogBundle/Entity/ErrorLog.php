<?php

namespace HealthCareAbroad\LogBundle\Entity;

class ErrorLog
{    
    /**
     * @var bigint $id
     */
    private $id;

    /**
     * @var smallint $errorType
     */
    private $errorType;

    /**
     * @var string $message
     */
    private $message;

    /**
     * @var text $stacktrace
     */
    private $stacktrace;

    /**
     * @var string $httpUserAgent
     */
    private $httpUserAgent;

    /**
     * @var string $remoteAddress
     */
    private $remoteAddress;

    /**
     * @var text $serverJSON
     */
    private $serverJSON;

    /**
     * @var datetime $dateCreated
     */
    private $dateCreated;


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
     * Set errorType
     *
     * @param smallint $errorType
     * @return ErrorLog
     */
    public function setErrorType($errorType)
    {
        $this->errorType = $errorType;
        return $this;
    }

    /**
     * Get errorType
     *
     * @return smallint 
     */
    public function getErrorType()
    {
        return $this->errorType;
    }

    /**
     * Set message
     *
     * @param string $message
     * @return ErrorLog
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
     * Set stacktrace
     *
     * @param text $stacktrace
     * @return ErrorLog
     */
    public function setStacktrace($stacktrace)
    {
        $this->stacktrace = $stacktrace;
        return $this;
    }

    /**
     * Get stacktrace
     *
     * @return text 
     */
    public function getStacktrace()
    {
        return $this->stacktrace;
    }

    /**
     * Set httpUserAgent
     *
     * @param string $httpUserAgent
     * @return ErrorLog
     */
    public function setHttpUserAgent($httpUserAgent)
    {
        $this->httpUserAgent = $httpUserAgent;
        return $this;
    }

    /**
     * Get httpUserAgent
     *
     * @return string 
     */
    public function getHttpUserAgent()
    {
        return $this->httpUserAgent;
    }

    /**
     * Set remoteAddress
     *
     * @param string $remoteAddress
     * @return ErrorLog
     */
    public function setRemoteAddress($remoteAddress)
    {
        $this->remoteAddress = $remoteAddress;
        return $this;
    }

    /**
     * Get remoteAddress
     *
     * @return string 
     */
    public function getRemoteAddress()
    {
        return $this->remoteAddress;
    }

    /**
     * Set serverJSON
     *
     * @param text $serverJSON
     * @return ErrorLog
     */
    public function setServerJSON($serverJSON)
    {
        $this->serverJSON = $serverJSON;
        return $this;
    }

    /**
     * Get serverJSON
     *
     * @return text 
     */
    public function getServerJSON()
    {
        return $this->serverJSON;
    }

    /**
     * Set dateCreated
     *
     * @param datetime $dateCreated
     * @return ErrorLog
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
}