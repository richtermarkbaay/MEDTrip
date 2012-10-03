<?php
namespace HealthCareAbroad\FrontendBundle\Entity;

class FrontendRouteVariable
{

    /**
     * @var bigint $frontendRouteId
     */
    private $frontendRouteId;

    /**
     * @var string $name
     */
    private $name;

    /**
     * @var bigint $value
     */
    private $value;

    /**
     * @var HealthCareAbroad\FrontendBundle\Entity\FrontendRoute
     */
    private $frontendRoute;


    /**
     * Set frontendRouteId
     *
     * @param bigint $frontendRouteId
     * @return FrontendRouteVariable
     */
    public function setFrontendRouteId($frontendRouteId)
    {
        $this->frontendRouteId = $frontendRouteId;
        return $this;
    }

    /**
     * Get frontendRouteId
     *
     * @return bigint 
     */
    public function getFrontendRouteId()
    {
        return $this->frontendRouteId;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return FrontendRouteVariable
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set value
     *
     * @param bigint $value
     * @return FrontendRouteVariable
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * Get value
     *
     * @return bigint 
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set frontendRoute
     *
     * @param HealthCareAbroad\FrontendBundle\Entity\FrontendRoute $frontendRoute
     * @return FrontendRouteVariable
     */
    public function setFrontendRoute(\HealthCareAbroad\FrontendBundle\Entity\FrontendRoute $frontendRoute = null)
    {
        $this->frontendRoute = $frontendRoute;
        $this->frontendRouteId = $frontendRoute->getId();
        return $this;
    }

    /**
     * Get frontendRoute
     *
     * @return HealthCareAbroad\FrontendBundle\Entity\FrontendRoute 
     */
    public function getFrontendRoute()
    {
        return $this->frontendRoute;
    }
}