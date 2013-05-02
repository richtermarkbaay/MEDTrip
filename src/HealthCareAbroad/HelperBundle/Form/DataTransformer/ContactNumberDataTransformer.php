<?php
namespace HealthCareAbroad\HelperBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class ContactNumberDataTransformer implements DataTransformerInterface
{
    private $defaultValue = array('country_code' => '', 'area_code' => '', 'number' => '');
    
    public function transform($data)
    {
        $data = \json_decode($data, true);
        if (!$data) {
        
         return $data = $this->defaultValue;
        }
        else{
            if($data){
                if (array_key_exists("country_code",$data)){
                    return $data;
                }
            }
            return $data = $this->defaultValue;    
        }
    }
    
    public function reverseTransform($value)
    {
        return \json_encode($value);
    }
}