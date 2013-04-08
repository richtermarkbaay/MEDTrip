<?php
/**
 * Twig extension for Country and Contact Number
 * @author Chaztine Blance
 */
namespace HealthCareAbroad\HelperBundle\Twig;

class AddressTextTwigExtension extends \Twig_Extension
{
    public function getName()
    {
        return 'convert_json_text';
    }
    
    
    public function getFunctions()
    {
        return array(
            'getAddress' => new \Twig_Function_Method($this, 'getAddressText'),
            'getContactText' => new \Twig_Function_Method($this, 'getContactText'),
        );
     }
     
     public function getAddressText($address)
     {
         if($address){
             $returnValue = "";
             $json_value = \json_decode($address, true);
             $returnValue .= $json_value['room_number'];
             $returnValue .= " " .$json_value['building'];
             $returnValue .= " " . $json_value['street'];
             
         }else{
             $returnValue = "";
         }
         
         return $returnValue;
     }
     
     public function getContactText($contact){
         
         $defaultValue = array('phone_number' => array ( 'number' => '', 'abbr' => ''), 'contact_number' => array ( 'number' => '', 'abbr' => ''));
         
         if($contact){
             $returnValue = "";
             $json_value = \json_decode($contact, true);
             $returnValue .= "+" .$json_value['country_code'];
             $returnValue .= " " .$json_value['area_code'];
             $returnValue .= " " . $json_value['number'];
              
         }else{
             $returnValue = "";
         }
          
         return $returnValue;
     }
}