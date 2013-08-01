<?php
namespace HealthCareAbroad\HelperBundle\Form\DataTransformer;

use HealthCareAbroad\HelperBundle\Entity\SocialMediaSites;

use Symfony\Component\Form\DataTransformerInterface;

class WebsitesDataTransformer implements  DataTransformerInterface
{   
    public function transform($value)
    {
        if(!$value) {
            $value = SocialMediaSites::getDefaultValues();
        } else {
            $value = \json_decode($value, true);
        }

        return $value;
    }

    public function reverseTransform($value)
    {
        if (!is_array($value)) {
            //throw new \Exception(__CLASS__.' expects $value to be an array, '.\gettype($value).' given');
            $value = SocialMediaSites::getDefaultValues();
        }

        return \json_encode($value);
    }
}