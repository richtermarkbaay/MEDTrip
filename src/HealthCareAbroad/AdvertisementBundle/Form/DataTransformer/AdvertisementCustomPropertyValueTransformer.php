<?php 

namespace HealthCareAbroad\AdvertisementBundle\Form\DataTransformer;

use HealthCareAbroad\AdvertisementBundle\Entity\AdvertisementPropertyValue;

use Doctrine\ORM\PersistentCollection;

use Doctrine\Common\Collections\ArrayCollection;

use Doctrine\ORM\EntityManager;

use HealthCareAbroad\AdvertisementBundle\Entity\AdvertisementPropertyName;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;
use Acme\TaskBundle\Entity\Issue;

class AdvertisementCustomPropertyValueTransformer implements DataTransformerInterface
{
    protected $em;
    
    /**
     * @var advertisementPropertyNameId
     */
    //private $advertisementPropertyNameId;
    
    /**
     */
    public function __construct($em = null)
    {
        $this->em = $em;
    }

    /**
     * Transforms an 
     *
     * @param  
     * @return 
     */
    public function transform($data)
    {
        $advertisementProperties = $data->getAdvertisementPropertyValues();
        
        if(!$this->em) {
            throw new \Exception('EntityManager is required in ' . get_class($this) . ' when editing Advertisement!');
        }

        $collectionPropertyValues = $collectionProperties = array();

        foreach($advertisementProperties as $each) {

            if(!$each->getValue()) {
                continue;
            }

            $property = $each->getAdvertisementPropertyName();
        
            if($property->getDataType()->getFormField() == 'entity') {
        
                $newValue = $this->em->getRepository($property->getDataClass())->find($each->getValue());
        
                if($property->getDataType()->getColumnType() == 'entity') {
                    $each->setValue($newValue);
                    continue;
                }

                if(!isset($collectionPropertyValues[$property->getId()])) {
                    $collectionProperties[] = $each;
                    $collectionPropertyValues[$property->getId()] = new ArrayCollection();
                }

                $collectionPropertyValues[$property->getId()]->add($newValue);
            }
        }

        foreach($collectionProperties as $each) {
            $propertyId = $each->getAdvertisementPropertyName()->getId();
            $each->setValue($collectionPropertyValues[$propertyId]);
        }

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
        $advertisementProperties = $data->getAdvertisementPropertyValues();
        $existingCollectionValues = $collectionValues = array();

        foreach($advertisementProperties as $each) {   
            $property = $each->getAdvertisementPropertyName();

            if($property->getDataType()->getColumnType() == 'collection') {
                
                if(is_object($each->getValue())) {
                    foreach($each->getValue() as $value) {
                        $collectionValues[$property->getId().'-'.$value->getId()] = $value;
                    }
                }

                if(is_string($each->getValue())) {
                    $existingCollectionValueObj[$property->getId().'-'.$each->getValue()] = $each; 
                }
            }
        }

        foreach($advertisementProperties as $i => $each) {
            $property = $each->getAdvertisementPropertyName();
            $dataType = $property->getDataType();

            if($dataType->getFormField() == 'entity') {

                if($dataType->getColumnType() == 'collection') {

                    if(is_object($each->getValue())) {
                        $isFirst = true;
                        foreach($each->getValue() as $value) {
                            if($isFirst) {
                                $each->setValue($each->getValue()->first()->getId());
                                $isFirst = false;
                            } else {
                                if(isset($existingCollectionValueObj[$property->getId().'-'.$value->getId()])) {
                                    $advertisementProperties->add($existingCollectionValueObj[$property->getId().'-'.$value->getId()]);
                                } else {
                                    $newObj = new AdvertisementPropertyValue();
                                    $newObj->setValue($value->getId());
                                    $newObj->setAdvertisementPropertyName($property);
                                    $newObj->setAdvertisement($data);
                                    $advertisementProperties->add($newObj);
                                }
                            }
                        }
                    }

                    elseif(is_string($each->getValue())) {
                        $advertisementProperties->remove($i);
                    }
 
                } else {
                   $each->setValue($each->getValue()->getId());
                }
            }
        }

       return $data;
    }
}