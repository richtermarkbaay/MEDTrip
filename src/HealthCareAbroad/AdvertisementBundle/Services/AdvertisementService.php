<?php
namespace HealthCareAbroad\AdvertisementBundle\Services;

use HealthCareAbroad\AdvertisementBundle\Entity\AdvertisementDenormalizedProperty;

use Doctrine\ORM\EntityManager;

use HealthCareAbroad\AdvertisementBundle\Entity\Advertisement;
use HealthCareAbroad\AdvertisementBundle\Repository\AdvertisementRepository;
use HealthCareAbroad\AdvertisementBundle\Entity\FeaturedListingAdvertisement;
use HealthCareAbroad\AdvertisementBundle\Services\AdvertisementFactory;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Service class for Advertisement
 *
 * @author Allejo Chris G. Velarde
 *
 */
class AdvertisementService
{
    
    private $doctrine;
    
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var AdvertisementRepository
     */
    private $repository;
    
    public function __construct($doctrine)
    {
        $this->doctrine = $doctrine;
        $this->em = $doctrine->getEntityManager();
        $this->repository = $this->em->getRepository('AdvertisementBundle:Advertisement');
    }
    
    public function save(Advertisement $advertisement)
    {        
        if($advertisement->getId()) {
            foreach($advertisement->getAdvertisementPropertyValues()->getDeleteDiff() as $value) {
                if($value->getAdvertisementPropertyName()->getName() != 'media_id') { // TODO
                    $this->em->remove($value);
                }
            }
        }

        $advertisement->setStatus(Advertisement::STATUS_ACTIVE);
        $this->em->persist($advertisement);
        $this->em->flush($advertisement);
        
        // Update Denormalized Advertisement Data
        $this->updateAdvertisementDenormalizedData($advertisement);
    }


    private function updateAdvertisementDenormalizedData(Advertisement $advertisement)
    {
        $data['id'] = $advertisement->getId();
        $data['institution_id'] = $advertisement->getInstitution()->getId();
        $data['advertisement_type_id'] = $advertisement->getAdvertisementType()->getId();
        $data['title'] = $advertisement->getTitle();
        $data['description'] = $advertisement->getDescription();
        $data['date_created'] = $advertisement->getDateCreated() ? $advertisement->getDateCreated()->format('Y-m-d H:i:s') : 'now()';
        $data['date_expiry'] = $advertisement->getDateExpiry()->format('Y-m-d H:i:s');
        $data['status'] = $advertisement->getStatus();


        foreach($advertisement->getAdvertisementPropertyValues() as $each) {
            $property = $each->getAdvertisementPropertyName();
            
            if($property->getDataType()->getColumnType() == 'collection') {
                $data[$property->getName()][] = (int)$each->getValue();
            } else {
                $data[$property->getName()] = $each->getValue();
            }
        }

        $onDuplicatePlaceholder = $columns = $valuesPlaceholder = '';
        
        foreach($data as $key => $value) {
            if(is_array($value)) {
                $data[$key] = json_encode($value);
            }

            $columns .= ",$key";
            $valuesPlaceholder .= ",:$key";
            $onDuplicatePlaceholder .= ",$key = :$key";
        }

        $columns = substr($columns, 1);
        $valuesPlaceholder = substr($valuesPlaceholder, 1);
        $onDuplicatePlaceholder = substr($onDuplicatePlaceholder, 1);
        
        $query = "INSERT INTO advertisement_denormalized_properties ($columns) VALUES($valuesPlaceholder) ON DUPLICATE KEY UPDATE $onDuplicatePlaceholder";
        $result = $this->em->getConnection()->executeQuery($query, $data);

//         $denormalizedAdvertisement = $this->em->getRepository('AdvertisementBundle:AdvertisementDenormalizedProperty')->find($advertisement->getId());

//         if(!$denormalizedAdvertisement) {
//             $denormalizedAdvertisement = new AdvertisementDenormalizedProperty();
//         }

//         $advertisementMethods = get_class_methods($advertisement);
//         $propertyValues = $advertisement->getAdvertisementPropertyValues();

//         $arrPropertyValues = array();
//         foreach($propertyValues as $each) {
//             $name = $each->getAdvertisementPropertyName()->getName();
//             $name = str_replace('_', '', $name);
    
//             if($each->getAdvertisementPropertyName()->getDataType()->getColumnType() == 'collection') {
//                 $arrPropertyValues[$name][] = (int)$each->getValue();
//             } else {
//                 $arrPropertyValues[$name] = $each->getValue();
//             }
//         }
    
//         foreach(get_class_methods($denormalizedAdvertisement) as $method) {
    
//             if(substr($method, 0, 3) == 'set') {
//                 $propertyName = substr($method, 3);
    
//                 $getMethod = 'get' . $propertyName;
    
//                 if(in_array($getMethod, $advertisementMethods)) {
//                     $value = $advertisement->{$getMethod}();
//                     $denormalizedAdvertisement->{$method}($value);
//                 } else {
//                     if(isset($arrPropertyValues[strtolower($propertyName)])) {
//                         $value = $arrPropertyValues[strtolower($propertyName)];
//                         if(is_array($value)) {
//                             $value = json_encode($value);
//                         }
//                     }
    
//                     else $value = '';
//                 }
                
//                 if($method == 'setMedia') {
//                     continue;
//                 }

//                 try { $denormalizedAdvertisement->{$method}($value);}
//                 catch(\Exception $e){}
//             }
//         }
    
//         $this->em->persist($denormalizedAdvertisement);
//         $this->em->flush($denormalizedAdvertisement);
    }
}