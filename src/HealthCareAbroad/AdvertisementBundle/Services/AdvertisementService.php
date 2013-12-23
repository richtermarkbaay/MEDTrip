<?php
namespace HealthCareAbroad\AdvertisementBundle\Services;

use Doctrine\Bundle\DoctrineBundle\Registry;

use HealthCareAbroad\AdvertisementBundle\Entity\AdvertisementStatuses;

use HealthCareAbroad\AdvertisementBundle\Exception\AdvertisementPropertyException;

use Doctrine\ORM\Query;

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
    /**
     * 
     * @var Registry
     */
    private $doctrine;
    
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var AdvertisementRepository
     */
    private $repository;
    
    public function __construct(Registry $doctrine)
    {
        $this->doctrine = $doctrine;
        $this->em = $doctrine->getEntityManager();
        $this->repository = $this->em->getRepository('AdvertisementBundle:Advertisement');
    }
    
    public function save(Advertisement $advertisement)
    {
        if($advertisement->getId()) {
            foreach($advertisement->getAdvertisementPropertyValues()->getDeleteDiff() as $value) {
                $this->em->remove($value);
            }
        }

        $advertisement->setStatus(AdvertisementStatuses::ACTIVE);
        $this->em->persist($advertisement);
        $this->em->flush();

        // Update Denormalized Advertisement Data
        $this->updateAdvertisementDenormalizedData($advertisement);
    }
    
    public function getDenomralizedPropertyById($advertisementId)
    {
        return $this->doctrine->getRepository('AdvertisementBundle:AdvertisementDenormalizedProperty')->find($advertisementId);
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


        
        // TODO - Enhance Saving Process. This can be done in 1 foreach.
        $collectionClasses = array();
        foreach($advertisement->getAdvertisementPropertyValues() as $each) {
            $property = $each->getAdvertisementPropertyName();
            
            if($property->getDataType()->getColumnType() == 'collection') {
                $data[$property->getName()][] = (int)$each->getValue();
                $collectionClasses[$property->getName()] = $property->getDataClass(); 
            } else {
                $data[$property->getName()] = $each->getValue();
            }
        }

        $onDuplicatePlaceholder = $columns = $valuesPlaceholder = '';
        
        foreach($data as $key => $value) {

            if(is_array($value)) {
                if(empty($value)) {
                    $data[$key] = ''; continue;
                }

                if(!$collectionClasses[$key]) {
                    throw AdvertisementPropertyException::emptyPropertyClass($key);
                }
                
                $qb = $this->em->createQueryBuilder();
                $query = $qb->select('a')->from($collectionClasses[$key], 'a')->where($qb->expr()->in('a.id', $value));

                $data[$key] = json_encode($query->getQuery()->getResult(Query::HYDRATE_ARRAY));
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

    }
}