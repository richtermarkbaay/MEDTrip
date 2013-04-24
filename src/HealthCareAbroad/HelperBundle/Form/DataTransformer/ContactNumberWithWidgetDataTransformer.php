<?php
namespace HealthCareAbroad\HelperBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class ContactNumberWithWidgetDataTransformer implements DataTransformerInterface
{
    private $defaultValue = array('phone_number' => array ( 'number' => '', 'abbr' => ''), 'contact_number' => array ( 'number' => '', 'abbr' => ''));
    public function transform($data)
    {
        $data = \json_decode($data, true);

        if (!$data) {
         return $data = $this->defaultValue;
        }
        else{
            if($data){
                if (array_key_exists("country_code",$data)){
                
                    return $this->defaultValue;
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