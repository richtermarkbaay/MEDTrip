<?php 
/**
 * 
 * @author Adelbert D. Silla
 *
 */
namespace HealthCareAbroad\AdvertisementBundle\Form\DataTransformer;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use HealthCareAbroad\AdvertisementBundle\Entity\AdvertisementPropertyValue;

class AdvertisementPropertyValuesTransformer implements DataTransformerInterface
{
    /**
     * @var em
     */
    private $em;

    /**
     */
    public function __construct($em = null)
    {
        $this->em = $em;
    }

    /**
     * Transforms AdvertisementPrortyValues value entity/collection IDs to objects
     *
     */
    public function transform($advertisement)
    {
        $advertisementPropertyValues = $advertisement->getAdvertisementPropertyValues();
        $advertisementProperties = $advertisement->getAdvertisementType()->getAdvertisementTypeConfigurations();

        if(!$this->em) {
            throw new \Exception('EntityManager is required in ' . get_class($this) . ' when editing Advertisement!');
        }

//         foreach($advertisementPropertyValues as $i => $each) {
//             var_dump($each->getAdvertisementPropertyName()->getName());
//             var_dump($each->getValue());
//         }
//         exit;

        $currentProperties = $collectionPropertyValues = $collectionProperties = array();

        foreach($advertisementPropertyValues as $each) {
            $property = $each->getAdvertisementPropertyName();
            $currentProperties[] = $property->getId();

            if(!$each->getValue()) {
                continue;
            }

            if($property->getDataType()->getFormField() == 'entity') {
                $newValue = $this->em->getRepository($property->getDataClass())->find($each->getValue());

                if($property->getDataType()->getColumnType() != 'collection') {                    
                    $each->setValue($property->getName() == 'media_id' ? array($newValue) : $newValue);
                    continue;
                }

                if(!isset($collectionPropertyValues[$property->getId()])) {
                    $collectionProperties[] = $each;
                    $collectionPropertyValues[$property->getId()] = new ArrayCollection();
                }

                $collectionPropertyValues[$property->getId()]->add($newValue);
            } else {
                if($property->getDataType()->getColumnType() == 'collection') {                    
                    $newValue = $each->getValue();
                    $defaultValue = new ArrayCollection();

                    if($property->getDataType()->getFormField() == 'file') {
                        $newValue = $this->em->getRepository($property->getDataClass())->find($each->getValue());
                    } elseif($property->getDataType()->getFormField() == 'choice') {
                        $defaultValue = array();
                    }

                    if(!isset($collectionPropertyValues[$property->getId()])) {
                        $collectionProperties[] = $each;
                        $collectionPropertyValues[$property->getId()] = $defaultValue;
                    }
                    
                    if(is_array($defaultValue)) {
                        $collectionPropertyValues[$property->getId()][] = $newValue;
                    } else {
                        $collectionPropertyValues[$property->getId()]->add($newValue);
                    }
                }
            }
        }

        foreach($collectionProperties as $each) {
            $propertyId = $each->getAdvertisementPropertyName()->getId();
            $each->setValue($collectionPropertyValues[$propertyId]);
        }

        foreach($advertisementProperties as $each) {
            if(!in_array($each->getId(), $currentProperties)) {
                $newObj = new AdvertisementPropertyValue();
                $newObj->setAdvertisementPropertyName($each);
                $newObj->setAdvertisement($advertisement);
                if($each->getDataType()->getColumnType() == 'collection') {
                    if($each->getDataType()->getFormField() == 'entity') {
                        $newObj->setValue(new ArrayCollection());                                      
                    } else {
                        $newObj->setValue(array());
                    }
                }
                $advertisement->addAdvertisementPropertyValue($newObj);
            }
        }
        
//         foreach($advertisementPropertyValues as $i => $each) {
//             var_dump($each);
//         }
//        exit;
        
        return $advertisement;
    }

    /**
     * Transforms AdvertisementPrortyValues value string entity/collection objects to IDs
     *
     */
    public function reverseTransform($advertisement)
    {
        $fileClassName = 'Symfony\Component\HttpFoundation\File\UploadedFile';
//         $advertisementPropertyValues = $advertisement->getAdvertisementPropertyValues();
//         $existingCollectionValues = $collectionValues = array();

//         foreach($advertisementPropertyValues as $each) {   

//             $property = $each->getAdvertisementPropertyName();

//             if($property->getDataType()->getColumnType() == 'collection') {
                
//                 if(is_object($each->getValue())) {
//                     foreach($each->getValue() as $value) {
//                         $collectionValues[$property->getId().'-'.$value->getId()] = $value;
//                     }
//                 }

//                 if(is_string($each->getValue())) {
//                     $existingCollectionValueObj[$property->getId().'-'.$each->getValue()] = $each; 
//                 }
//             }
//         }

//         foreach($advertisementPropertyValues as $i => $each) {
//             $property = $each->getAdvertisementPropertyName();
//             $advertisementType = $property->getDataType();
//             $config = json_decode($property->getPropertyConfig(), true);

//             if(is_null($each->getValue())) {
//                 $advertisementPropertyValues->remove($i);
//                 continue;
//             }

//             if($advertisementType->getFormField() == 'entity') {

//                 if($advertisementType->getColumnType() != 'collection') {
//                     if($property->getName() != 'media_id') {
//                         $each->setValue($each->getValue()->getId());
//                     }
//                 } else { 

//                     if(is_string($each->getValue())) {
//                         $advertisementPropertyValues->remove($i);
//                         continue;
//                     }

//                     elseif(is_object($each->getValue())) {

//                         if(!count($each->getValue())) {
//                             $advertisementPropertyValues->remove($i);
//                             continue;
//                         }

//                         $isFirst = true;
//                         foreach($each->getValue() as $value) {
//                             if($isFirst) {
//                                 $each->setValue($value->getId());
//                                 $isFirst = false;
//                             } else {
//                                 if(isset($existingCollectionValueObj[$property->getId().'-'.$value->getId()])) {
//                                     $advertisementPropertyValues->add($existingCollectionValueObj[$property->getId().'-'.$value->getId()]);
//                                 } else {
//                                     $newObj = new AdvertisementPropertyValue();
//                                     $newObj->setValue($value->getId());
//                                     $newObj->setAdvertisementPropertyName($property);
//                                     $newObj->setAdvertisement($advertisement);
//                                     $advertisementPropertyValues->add($newObj);
//                                 }
//                             }
//                         }
//                     }
//                 }
//             }
//         }
        
        $advertisementPropertyValues = $advertisement->getAdvertisementPropertyValues();
        $existingCollectionValues = $collectionValues = array();

        foreach($advertisementPropertyValues as $each) {

            $property = $each->getAdvertisementPropertyName();

            if($property->getDataType()->getColumnType() == 'collection') {

                if(is_object($each->getValue()) && $property->getDataType()->getFormField() == 'entity') {
                    foreach($each->getValue() as $value) {
                        $collectionValues[$property->getId().'-'.$value->getId()] = $value;
                    }
                }
                if(is_string($each->getValue())) {
                    $existingCollectionValueObj[$property->getId().'-'.$each->getValue()] = $each;
                }
            }
        }
        
        foreach($advertisementPropertyValues as $i => $each) {
            $property = $each->getAdvertisementPropertyName();
            $advertisementType = $property->getDataType();
            //$config = json_decode($property->getPropertyConfig(), true);

            // If value is null OR empty array OR empty string with columnType collection
            if((is_null($each->getValue()) && $property->getName() != 'media_id') || (is_array($each->getValue()) && !count($each->getValue())) || (is_string($each->getValue()) && $advertisementType->getColumnType() == 'collection')) {
                $advertisementPropertyValues->remove($i); continue;
            }

            if(is_array($each->getValue()) || (is_object($each->getValue()) && $advertisementType->getColumnType() == 'collection' && $advertisementType->getFormField() == 'file')) { // If value is array()

                $isFirst = true;
                foreach($each->getValue() as $i => $value) {
                    if($value) {
                        $newObj = new AdvertisementPropertyValue();
                        $newObj->setValue($value);
                        $newObj->setAdvertisementPropertyName($property);
                        $newObj->setAdvertisement($advertisement);
                        
                        if($isFirst) {
                            $isFirst = false;
                            $each->setValue($value);
                        } else {
                            $advertisementPropertyValues->add($newObj);
                        }                        
                    }
                }

            } elseif (is_object($each->getValue())) { // If value is object

                if ($advertisementType->getColumnType() == 'entity' && $fileClassName != get_class($each->getValue())) {
                   $each->setValue($each->getValue()->getId());

                } elseif($advertisementType->getColumnType() == 'collection' && $advertisementType->getFormField() == 'entity') {

                    $isFirst = true;
                    foreach($each->getValue() as $value) {
                        if($isFirst) {
                            $isFirst = false;
                            $each->setValue($value->getId());
                        } else {
                            if(isset($existingCollectionValueObj[$property->getId().'-'.$value->getId()])) {
                                $advertisementPropertyValues->add($existingCollectionValueObj[$property->getId().'-'.$value->getId()]);
                            } else {
                                $newObj = new AdvertisementPropertyValue();
                                $newObj->setValue($value->getId());
                                $newObj->setAdvertisementPropertyName($property);
                                $newObj->setAdvertisement($advertisement);
                                $advertisementPropertyValues->add($newObj);
                            }
                        }
                    }

                    if(is_object($each->getValue())) {
                        $arrValue =  $each->getValue()->toArray();
                    }

                    if(isset($arrValue) && empty($arrValue)) {
                        $advertisementPropertyValues->remove($i);
                    }
                }
            }
        }

//         foreach($advertisementPropertyValues as $each) {
//             var_dump($each);
//         }
//         exit;

        return $advertisement;
    }
}