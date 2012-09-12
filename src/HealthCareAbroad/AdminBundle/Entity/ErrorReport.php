<?php

namespace HealthCareAbroad\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HealthCareAbroad\AdminBundle\Entity\ErrorReport
 */
class ErrorReport
{
    /**
     * @var integer $id
     */
    private $id;

    /**
     * @var string $reporterName
     */
    private $reporterName;

    /**
     * @var text $details
     */
    private $details;

    /**
     * @var bigint $loggedUserId
     */
    private $loggedUserId;

    /**
     * @var datetime $dateCreated
     */
    private $dateCreated;

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
     * Set reporterName
     *
     * @param string $reporterName
     * @return ErrorReport
     */
    public function setReporterName($reporterName)
    {
        $this->reporterName = $reporterName;
        return $this;
    }

    /**
     * Get reporterName
     *
     * @return string 
     */
    public function getReporterName()
    {
        return $this->reporterName;
    }

    /**
     * Set details
     *
     * @param text $details
     * @return ErrorReport
     */
    public function setDetails($details)
    {
        $this->details = $details;
        return $this;
    }

    /**
     * Get details
     *
     * @return text 
     */
    public function getDetails()
    {
        return $this->details;
    }

    /**
     * Set loggedUserId
     *
     * @param bigint $loggedUserId
     * @return ErrorReport
     */
    public function setLoggedUserId($loggedUserId)
    {
        $this->loggedUserId = $loggedUserId;
        return $this;
    }

    /**
     * Get loggedUserId
     *
     * @return bigint 
     */
    public function getLoggedUserId()
    {
        return $this->loggedUserId;
    }

    /**
     * Set dateCreated
     *
     * @param datetime $dateCreated
     * @return ErrorReport
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
     * @return ErrorReport
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