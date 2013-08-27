<?php
namespace HealthCareAbroad\HelperBundle\Form\DataTransformer;

use HealthCareAbroad\HelperBundle\Entity\SocialMediaSites;

use Symfony\Component\Form\DataTransformerInterface;

class SocialMediaSitesTransformer implements  DataTransformerInterface
{   
    public function transform($value)
    {
        $value = \json_decode($value, true);

        if(!\is_array($value) || empty($value)) {
            $value = SocialMediaSites::getDefaultValues();                
        }

        return $value;
    }

    public function reverseTransform($value)
    {
        if (!\is_array($value) || empty($value)) {
            $value = SocialMediaSites::getDefaultValues();
        }

        return \json_encode($value);
    }
}