<?php
/**
 * 
 * @author Allejo Chris G. Velarde
 *
 */
namespace HealthCareAbroad\HelperBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

/**
 * Data transformer for detailed street address which has the following fields:
 *     - room_number
 *     - building
 *     - street
 */
class DetailedStreetAddressDataTransformer implements DataTransformerInterface
{
    /**
     * Transform current data for viewing. Expected data is in JSON string format, if not, $value will be default value of street field.
     * $value will be transformed to an array containing room_number, building, street. i.e. array('room_number' => '', 'building' => '', 'street' => '')
     * 
     * @see Symfony\Component\Form.DataTransformerInterface::transform()
     */
    public function transform($value)
    {
        $json_value = \json_decode($value, true);
        
        if (!$json_value) {
            $json_value = array('room_number' => '', 'building' => '', 'street' => '');
        }
        return $json_value;
    }
    
    public function reverseTransform($value)
    {
        if($value['street'] == ''){
            return null;
        }
        
        return \json_encode($value);   
        
    }
}