<?php 

namespace HealthCareAbroad\AdvertisementBundle\Form\DataTransformer;

use HealthCareAbroad\AdvertisementBundle\Entity\AdvertisementPropertyName;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;
use Acme\TaskBundle\Entity\Issue;

class AdvertisementCustomPropertyValueTransformer implements DataTransformerInterface
{
    /**
     * @var advertisementPropertyNameId
     */
    //private $advertisementPropertyNameId;

    /**
     * @param ObjectManager $om
     */
    public function __construct()
    {
        //$this->advertisementPropertyNameId = $advertisementPropertyNameId;
    }

    /**
     * Transforms an 
     *
     * @param  
     * @return 
     */
    public function transform($data)
    {
        return $data;
    }

    /**
     * Transforms a string 
     *
     * @param  
     * @return 
     * @throws TransformationFailedException if object (issue) is not found.
     */
    public function reverseTransform($data)
    {
        echo 'trasformer';

        $values = array();
        
        foreach($data->getValue() as $each) {
            if(is_object($each)) {
                $value[] = $each->getId();
            } else {
                $value = $data->getValue()->getId(); 
            }
        } 
        
//         if(is_array($value)) {
//             $value = implode(',', $value);
//         }

        $data->setValue($value);

//         var_dump($data); exit;
//         if(count($data->getValue())) {
//             foreach($each['value'] as $value) {
//                 array_push($data['advertisementPropertyValues'], array(
//                     'advertisementPropertyName' => $each['advertisementPropertyName'],
//                     'value' => $vaue
//                 ));
//             }
//         } else {
            
//         }
        
        //var_dump($data['advertisementPropertyValues']); exit;
        
        return $data;
    }
}