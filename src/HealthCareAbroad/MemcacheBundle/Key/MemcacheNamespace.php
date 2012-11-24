<?php
namespace HealthCareAbroad\MemcacheBundle\Key;

use HealthCareAbroad\MemcacheBundle\Key\ConfigurationDefaults;

class MemcacheNamespace
{
     private $name;
     private $pattern;
     private $variables;
     private $separator;

     public function __construct()
     {
         $this->separator = ConfigurationDefaults::DEFAULT_SEPARATOR;
     }
     
     public function setName($name)
     {
         $this->name = $name;
         
         return $this;
     }
     
     public function getName()
     {
         return $this->name;
     }
     
     public function setPattern($pattern)
     {
         $this->pattern = $pattern;
         
         return $this;
     }
     
     public function getPattern()
     {
         return $this->pattern;
     }
     
     public function setVariables($variables)
     {
         $this->variables = $variables;
         
         return $this;
     }
     
     public function getVariables()
     {
         return $this->variables;
     }
     
     public function setSeparator($separator) 
     {
         $this->separator = $separator;

         return $this;
     }
}