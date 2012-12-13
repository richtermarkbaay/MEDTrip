<?php
namespace HealthCareAbroad\HelperBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class WebsitesDataTransformer implements  DataTransformerInterface
{
    public function transform($value)
    {
        
        $jsonValue = \json_decode($value, true);
        
        if (!$jsonValue) {
            
            $jsonValue = array('main' => '', 'facebook' => '','twitter' => '');
        }
        
        return $jsonValue;
    }
    
    public function reverseTransform($value)
    {
        if (!is_array($value)) {
            throw new \Exception(__CLASS__.' expects $value to be an array, '.\gettype($value).' given');
        }
        
        return \json_encode($value);
    }
}