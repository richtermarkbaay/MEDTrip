<?php

namespace HealthCareAbroad\HelperBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HealthCareAbroad\HelperBundle\Entity\HelpText
 */
class HelperText
{
    const STATUS_ACTIVE = 1;
    
    const STATUS_INACTIVE = 0;
    
    /**
     * @var integer $id
     */
    private $id;

    /**
     * @var smallint $route
     */
    private $route;

    /**
     * @var string $details
     */
    private $details;

    /**
     * @var smallint $status
     */
    private $status;


    /**
     * Set id
     *
     * @param integer $id
     * @return HelpText
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

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
     * Set route
     *
     * @param smallint $route
     * @return HelpText
     */
    public function setRoute($route)
    {
        $this->route = $route;
        return $this;
    }

    /**
     * Get route
     *
     * @return smallint 
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * Set details
     *
     * @param string $details
     * @return HelpText
     */
    public function setDetails($details)
    {
        $this->details = $details;
        return $this;
    }

    /**
     * Get details
     *
     * @return string 
     */
    public function getDetails()
    {
        return $this->details;
    }

    /**
     * Set status
     *
     * @param smallint $status
     * @return HelpText
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