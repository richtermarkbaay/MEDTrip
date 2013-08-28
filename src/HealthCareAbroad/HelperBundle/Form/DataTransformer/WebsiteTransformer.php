<?php
namespace HealthCareAbroad\HelperBundle\Form\DataTransformer;

use HealthCareAbroad\HelperBundle\Entity\SocialMediaSites;

use Symfony\Component\Form\DataTransformerInterface;

class WebsiteTransformer implements  DataTransformerInterface
{   
    const SCHEME = 'http'; 
    
    public function transform($value)
    {        
        return $this->formatUrl($value);
    }
    
    public function reverseTransform($value)
    {
        return $this->formatUrl($value);
    }
    
    private function formatUrl($value)
    {
        if(strpos($value, self::SCHEME) === false) {
            $value = self::SCHEME . '://' . trim($value);
        }

        return $value;
    }
}