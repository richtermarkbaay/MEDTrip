<?php
namespace HealthCareAbroad\AdvertisementBundle\Services;

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

                if($key == 'highlight_doctors') {
                    $query = $qb->select('a,b,c')
                                ->from($collectionClasses[$key], 'a')
                                ->leftJoin('a.specializations', 'b')
                                ->leftJoin('a.media', 'c')
                                ->where($qb->expr()->in('a.id', $value));
                } else {
                    $query = $qb->select('a')->from($collectionClasses[$key], 'a')->where($qb->expr()->in('a.id', $value));
                }

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