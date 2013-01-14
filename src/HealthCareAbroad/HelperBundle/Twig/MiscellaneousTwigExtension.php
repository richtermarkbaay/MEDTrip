<?php
/**
 * Twig extension for miscellaneous functions or filters
 * 
 * @author Allejo Chris G. Velarde
 */
namespace HealthCareAbroad\HelperBundle\Twig;

use HealthCareAbroad\InstitutionBundle\Entity\Institution;

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
            'getClassLabels' => new \Twig_Function_Method($this, 'getClassLabels'),
            'base64_encode' => new \Twig_Function_Method($this, 'base64_encode'),
            'unserialize' => new \Twig_Function_Method($this, 'unserialize'),
            'institution_address_to_array' => new \Twig_Function_Method($this, 'institution_address_to_array'),
            'json_decode' => new  \Twig_Function_Method($this, 'json_decode'),
            'json_encode' => new  \Twig_Function_Method($this, 'json_encode'),
            'institution_websites_to_array' => new \Twig_Function_Method($this, 'institution_websites_to_array'),
        );
    }
    
    public function base64_encode($s) {
        return \base64_encode($s);
    }
    
    public function getClass($object)
    {
        return \get_class($object);
    }
    
    public function getClassLabels($classKeys=array())
    {
        $labels = array();
        foreach ($classKeys as $classKey) {
            $labels[$classKey] = $this->getClassLabel($classKey);
        }
        
        return $labels;
    }
    
    /**
     * Get label for class.
     * 
     * @param mixed $classKey
     * @param boolean $plural
     */
    public function getClassLabel($classKey)
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
            throw new \Exception("Unable to find label for class {$classKey}");   
        }
        
        return $this->classLabels[$classKey];
    }
    
    public function getName()
    {
        return 'miscellaneous';
    }
    
    public function json_encode($jsonArray)
    {
        echo  \json_encode($jsonArray);

        exit;
    }
    
    public function json_decode($jsonData)
    {
        return json_decode($jsonData, true);
    }
    
    /**
     * Convert institution address to array
     *     - city
     *     - state
     *     - country
     *     - zip code
     * 
     * @param Institution $institution
     */
    public function institution_address_to_array(Institution $institution)
    {
        $elements = array();
        
        if ($institution->getCity()) {
            $elements['city'] = $institution->getCity()->getName();
        }
        
        if ('' != $institution->getState()) {
            $elements['state'] = $institution->getState();
        }
        
        if ($institution->getCountry()) {
            $elements['country'] = $institution->getCountry()->getName();
        }
        
        if (0 != $institution->getZipCode() || '' != $institution->getZipCode()) {
            $elements['zip_code'] = $institution->getZipCode();
        }
        
        return $elements;
    }
    
    public function institution_websites_to_array(Institution $institution)
    {
        $websites = \json_decode($institution->getWebsites(), true);
        \array_walk($websites, function(&$v, $key){
            // if it matches http or https
            if (! \preg_match('/^https?:\/\//i', $v)) {
                $v = 'http://'.$v;
            }
        });
        
        return $websites;
    }
}