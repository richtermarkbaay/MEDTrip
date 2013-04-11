<?php

namespace HealthCareAbroad\InstitutionBundle\Entity;

/**
 * Object representation of an entry in registrationSteps.yml
 * 
 * @author Allejo Chris G. Velarde
 */
class SignUpStep
{
    private $stepNumber = 0;
    
    private $route;
    
    private $label;
    
    /**
     * @var bool
     */
    private $sub;
    
    /**
     * @var SignUpStep
     */
    private $parent;
    
    public function setStepNumber($v)
    {
        $this->stepNumber = (int) $v;
        return $this;
    }
    
    public function getStepNumber()
    {
        return $this->stepNumber;
    }
    
    public function setRoute($v)
    {
        $this->route = $v;
        return $this;
    }
    
    public function getRoute()
    {
        return $this->route;
    }
    
    public function setLabel($v)
    {
        $this->label = $v;
        return $this;
    }
    
    public function getLabel()
    {
        return $this->label;
    }
    
    public function setSub($v)
    {
        $this->sub = (bool) $v;
        return $this;
    }
    
    public function isSub()
    {
        return $this->sub;
    }
    
    public function setParent(SignUpStep $v=null)
    {
        $this->parent = $v;
        return $this;
    }
    
    public function getParent()
    {
        return $this->parent;
    }
}