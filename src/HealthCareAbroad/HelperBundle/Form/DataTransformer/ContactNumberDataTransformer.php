<?php
namespace HealthCareAbroad\HelperBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class ContactNumberDataTransformer implements DataTransformerInterface
{
    public function transform($data)
    {
        $data = \json_decode($data, true);
        
        if (!$data) {
            $data = array('country_code' => '', 'area_code' => '', 'number' => '');
        }
        
        return $data;
    }
    
    public function reverseTransform($value)
    {
        return \json_encode($value);
    }
}