<?php 
/**
 * 
 * @author Adelbert D. Silla
 *
 */
namespace HealthCareAbroad\AdvertisementBundle\Form\DataTransformer;

use HealthCareAbroad\MediaBundle\Entity\Media;

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

        $currentProperties = $collectionPropertyValues = $collectionProperties = array();

        foreach($advertisementPropertyValues as $each) {
            $property = $each->getAdvertisementPropertyName();
            $currentProperties[] = $property->getId();

            if(!$each->getValue()) {
                continue;
            }
            
            if($property->getName() == 'highlights') {
                $newValue = json_decode($each->getValue(), true);
                foreach($newValue as $i => $highlight) {
                    if($mediaId = $highlight['icon']) {
                        $media = $this->em->getRepository('MediaBundle:Media')->find($mediaId);
                        $newValue[$i]['icon'] = $media;                        
                    }
                }
 
                $each->setValue($newValue);
            }

            if($property->getDataType()->getFormField() == 'entity') {
                $newValue = $this->em->getRepository($property->getDataClass())->find($each->getValue());

                if($property->getDataType()->getColumnType() != 'collection') {
                    $each->setValue($property->getName() == 'media_id' ? $newValue : $newValue);
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
                        $collectionPropertyValues[$property->getId()]->set($each->getId(), $newValue);
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
                
                if($each->getName() == 'media_id') {
                    $newObj->setValue(new Media());
                
                } elseif ($each->getName() == 'highlights') {
                    $highlights = new ArrayCollection();
                    $highlights->add(array());
                    $newObj->setValue($highlights);
                }
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

        return $advertisement;
    }

    /**
     * Transforms AdvertisementPrortyValues value string entity/collection objects to IDs
     *
     */
    public function reverseTransform($advertisement)
    {
        if(is_null($advertisement->getDescription())) {
            $advertisement->setDescription('');
        }

        
        $fileClassName = 'Symfony\Component\HttpFoundation\File\UploadedFile';        
        $advertisementPropertyValues = $advertisement->getAdvertisementPropertyValues();
        $existingCollectionValues = $collectionValues = array();

        
        foreach($advertisementPropertyValues as $i => $each) {
            $property = $each->getAdvertisementPropertyName();

            if($property->getDataType()->getColumnType() == 'collection' && is_string($each->getValue())) {
                $existingCollectionValueObj[$property->getId().'-'.$each->getValue()] = $each;
            }
        }

        foreach($advertisementPropertyValues as $i => $each) {
            
            $property = $each->getAdvertisementPropertyName();
            $advertisementType = $property->getDataType();

            if(is_null($each->getValue())) {
                $each->setValue('');
            }

            // If value is empty array OR empty string with columnType collection
            if((is_array($each->getValue()) && !count($each->getValue())) || (is_string($each->getValue()) && $advertisementType->getColumnType() == 'collection')) {
                $advertisementPropertyValues->remove($i); continue;

            } elseif (is_object($each->getValue()) || is_array($each->getValue())) { // If value is object

                if($advertisementType->getColumnType() == 'collection') {
                    $arrValue =  is_object($each->getValue()) ? $each->getValue()->toArray() : $each->getValue();
                    $isFirst = true;
                    foreach($arrValue as $key => $value) {

                        $hasId = method_exists($value, 'getId');
                        $newValue = $hasId ? $value->getId() : $value;

                        if(($advertisementType->getFormField() == 'file' && is_numeric($key) && isset($arrValue['image'.$key]) && $arrValue['image'.$key]) || is_null($value)) {
                            continue;
                        }

                        if($isFirst) {
                            $isFirst = false;
                            $each->setValue($newValue);
                        } else {
                            if($hasId && isset($existingCollectionValueObj[$property->getId().'-'.$value->getId()])) {
                                $advertisementPropertyValues->add($existingCollectionValueObj[$property->getId().'-'.$value->getId()]);
                            } else {
                                $newObj = new AdvertisementPropertyValue();
                                $newObj->setValue($newValue);
                                $newObj->setAdvertisementPropertyName($property);
                                $newObj->setAdvertisement($advertisement);
                                $advertisementPropertyValues->add($newObj);
                            }
                        }
                    }

                    if(isset($arrValue) && empty($arrValue)) {
                        $advertisementPropertyValues->remove($i);
                    }                    

                } elseif ($advertisementType->getColumnType() == 'entity' && $fileClassName != get_class($each->getValue())) {
                    if($each->getValue()->getId()) $each->setValue($each->getValue()->getId());
                    else $advertisementPropertyValues->remove($i);
                }
            }
        }

//         foreach($advertisementPropertyValues as $i => $each) {
//             var_dump($each->getValue()); 
//         }
         //exit;

        return $advertisement;
    }
}