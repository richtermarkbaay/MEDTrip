<?php
/**
 * Twig extension for miscellaneous functions or filters
 * 
 * @author Allejo Chris G. Velarde
 */
namespace HealthCareAbroad\HelperBundle\Twig;

class MiscellaneousTwigExtension extends \Twig_Extension
{
    private $classKeys;
    private $classLabels;
    
    public function setClassKeys($keys)
    {
        $this->classKeys = $keys;
    }
    
    public function setClassLabels($labels)
    {
        $this->classLabels = $labels;
    }
    
    public function getFunctions()
    {
        return array(
            'getClass' => new \Twig_Function_Method($this, 'getClass'),
            'getClassLabel' => new \Twig_Function_Method($this, 'getClassLabel'),
            'base64_encode' => new \Twig_Function_Method($this, 'base64_encode'),
            'unserialize' => new \Twig_Function_Method($this, 'unserialize'),
            'json_decode' => new  \Twig_Function_Method($this, 'json_decode')
        );
    }
    
    public function base64_encode($s) {
        return \base64_encode($s);
    }
    
    public function getClass($object)
    {
        return \get_class($object);
    }
    
    /**
     * Get label for class.
     * 
     * @param mixed $classKey
     * @param boolean $plural
     */
    public function getClassLabel($classKey, $plural=false)
    {
        if (\is_object($classKey)) {
            $r = \array_flip($this->classKeys);
            $class = $this->getClass($classKey);
            if (!\array_key_exists($class, $r)) {
                throw new \Exception("Unable to find class key for class {$class}");
            }
            
            $classKey = $r[$class];
        }
        
        if (!\array_key_exists($classKey, $this->classLabels)) {
            throw new \Exception("Unable to find label for class {$class}");   
        }
        
        return $plural && count($this->classLabels[$classKey]) > 1 
            ? $this->classLabels[$classKey][1]
            :$this->classLabels[$classKey][0];
    }
    
    public function getName()
    {
        return 'miscellaneous';
    }
    
    public function json_decode($jsonData)
    {
        return json_decode($jsonData, true);
    }
}