<?php
/**
 * Twig extension for miscellaneous functions or filters
 * 
 * @author Allejo Chris G. Velarde
 */
namespace HealthCareAbroad\HelperBundle\Twig;

class MiscellaneousTwigExtension extends \Twig_Extension
{
    public function getFunctions()
    {
        return array(
            'getClass' => new \Twig_Function_Method($this, 'getClass'),
            'base64_encode' => new \Twig_Function_Method($this, 'base64_encode'),
            'unserialize' => new \Twig_Function_Method($this, 'unserialize')
        );
    }
    
    public function base64_encode($s) {
        return \base64_encode($s);
    }
    
    public function getClass($object)
    {
        return \get_class($object);
    }
    
    public function getName()
    {
        return 'miscellaneous';
    }
    
    public function unserialize($a)
    {
        var_dump($a); exit;
        return \unserialize($a);
    }
}