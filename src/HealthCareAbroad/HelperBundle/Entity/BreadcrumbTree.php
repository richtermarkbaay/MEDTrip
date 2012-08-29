<?php
namespace HealthCareAbroad\HelperBundle\Entity;

use Gedmo\Tree\Node;

class BreadcrumbTree implements Node
{
    /**
     * @var integer $id
     */
    private $id;

    /**
     * @var string $route
     */
    private $route;

    /**
     * @var string $label
     */
    private $label;

    /**
     * @var integer $rootId
     */
    private $rootId;

    /**
     * @var integer $leftValue
     */
    private $leftValue;

    /**
     * @var integer $rightValue
     */
    private $rightValue;

    /**
     * @var integer $level
     */
    private $level;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $children;

    /**
     * @var HealthCareAbroad\HelperBundle\Entity\BreadcrumbTree
     */
    private $parent;

    public function __construct()
    {
        $this->children = new \Doctrine\Common\Collections\ArrayCollection();
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

    /**
     * Set level
     *
     * @param integer $level
     * @return BreadcrumbTree
     */
    public function setLevel($level)
    {
        $this->level = $level;
        return $this;
    }

    /**
     * Get level
     *
     * @return integer 
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * Add children
     *
     * @param HealthCareAbroad\HelperBundle\Entity\BreadcrumbTree $children
     * @return BreadcrumbTree
     */
    public function addChildren(\HealthCareAbroad\HelperBundle\Entity\BreadcrumbTree $children)
    {
        $this->children[] = $children;
        return $this;
    }

    /**
     * Remove children
     *
     * @param HealthCareAbroad\HelperBundle\Entity\BreadcrumbTree $children
     */
    public function removeChildren(\HealthCareAbroad\HelperBundle\Entity\BreadcrumbTree $children)
    {
        $this->children->removeElement($children);
    }

    /**
     * Get children
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Set parent
     *
     * @param HealthCareAbroad\HelperBundle\Entity\BreadcrumbTree $parent
     * @return BreadcrumbTree
     */
    public function setParent(\HealthCareAbroad\HelperBundle\Entity\BreadcrumbTree $parent = null)
    {
        $this->parent = $parent;
        return $this;
    }

    /**
     * Get parent
     *
     * @return HealthCareAbroad\HelperBundle\Entity\BreadcrumbTree 
     */
    public function getParent()
    {
        return $this->parent;
    }
}