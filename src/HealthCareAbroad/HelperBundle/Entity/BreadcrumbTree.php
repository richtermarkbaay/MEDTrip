<?php
namespace HealthCareAbroad\HelperBundle\Entity;

use DoctrineExtensions\NestedSet\MultipleRootNode;

class BreadcrumbTree implements MultipleRootNode
{
    /**
     * @var integer $id
     */
    private $id;

    /**
     * @var string $route
     *
    private $route;

    /**
     * @var string $label
     */
    private $label;

    /**
     * @var integer $leftValue
     */
    private $leftValue;

    /**
     * @var integer $rightValue
     */
    private $rightValue;
    
    /**
     * @var string $route
     */
    private $route;


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
     * @param string $route
     * @return BreadcrumbTree
     */
    public function setRoute($route)
    {
        $this->route = $route;
        return $this;
    }

    /**
     * Get route
     *
     * @return string 
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * Set label
     *
     * @param string $label
     * @return BreadcrumbTree
     */
    public function setLabel($label)
    {
        $this->label = $label;
        return $this;
    }

    /**
     * Get label
     *
     * @return string 
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Set leftValue
     *
     * @param integer $leftValue
     * @return BreadcrumbTree
     */
    public function setLeftValue($leftValue)
    {
        $this->leftValue = $leftValue;
        return $this;
    }

    /**
     * Get leftValue
     *
     * @return integer 
     */
    public function getLeftValue()
    {
        return $this->leftValue;
    }

    /**
     * Set rightValue
     *
     * @param integer $rightValue
     * @return BreadcrumbTree
     */
    public function setRightValue($rightValue)
    {
        $this->rightValue = $rightValue;
        return $this;
    }

    /**
     * Get rightValue
     *
     * @return integer 
     */
    public function getRightValue()
    {
        return $this->rightValue;
    }
    
    public function __toString()
    {
        return $this->label;
    }
    
    
    /**
     * @var integer $rootId
     */
    private $rootId;


    /**
     * Set rootId
     *
     * @param integer $rootId
     * @return BreadcrumbTree
     */
    public function setRootId($rootId)
    {
        $this->rootId = $rootId;
        return $this;
    }

    /**
     * Get rootId
     *
     * @return integer 
     */
    public function getRootId()
    {
        return $this->rootId;
    }
    
    public function getRootValue() {
        return $this->rootId;
    }
    public function setRootValue($root) {
        $this->rootId = $root;
    }
}