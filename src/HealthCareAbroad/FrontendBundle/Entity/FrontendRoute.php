<?php
namespace HealthCareAbroad\FrontendBundle\Entity;

class FrontendRoute
{

    /**
     * @var bigint $id
     */
    private $id;

    /**
     * @var string $uri
     */
    private $uri;

    /**
     * @var string $controller
     */
    private $controller;

    /**
     * @var text $variables
     */
    private $variables;

    /**
     * @var smallint $status
     */
    private $status;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $frontendRouteVariables;

    public function __construct()
    {
        $this->frontendRouteVariables = new \Doctrine\Common\Collections\ArrayCollection();
    }

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
     * Set uri
     *
     * @param string $uri
     * @return FrontendRoute
     */
    public function setUri($uri)
    {
        $this->uri = $uri;
        return $this;
    }

    /**
     * Get uri
     *
     * @return string
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * Set controller
     *
     * @param string $controller
     * @return FrontendRoute
     */
    public function setController($controller)
    {
        $this->controller = $controller;
        return $this;
    }

    /**
     * Get controller
     *
     * @return string
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * Set variables
     *
     * @param text $variables
     * @return FrontendRoute
     */
    public function setVariables($variables)
    {
        $this->variables = $variables;
        return $this;
    }

    /**
     * Get variables
     *
     * @return text
     */
    public function getVariables()
    {
        return $this->variables;
    }

    /**
     * Set status
     *
     * @param smallint $status
     * @return FrontendRoute
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
     * Add frontendRouteVariables
     *
     * @param HealthCareAbroad\FrontendBundle\Entity\FrontendRouteVariable $frontendRouteVariables
     * @return FrontendRoute
     */
    public function addFrontendRouteVariable(\HealthCareAbroad\FrontendBundle\Entity\FrontendRouteVariable $frontendRouteVariables)
    {
        $this->frontendRouteVariables[] = $frontendRouteVariables;
        return $this;
    }

    /**
     * Remove frontendRouteVariables
     *
     * @param HealthCareAbroad\FrontendBundle\Entity\FrontendRouteVariable $frontendRouteVariables
     */
    public function removeFrontendRouteVariable(\HealthCareAbroad\FrontendBundle\Entity\FrontendRouteVariable $frontendRouteVariables)
    {
        $this->frontendRouteVariables->removeElement($frontendRouteVariables);
    }

    /**
     * Get frontendRouteVariables
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getFrontendRouteVariables()
    {
        return $this->frontendRouteVariables;
    }
}