<?php
/**
 * Factory class for advertisement. This class is only concerned with creating advertisement instance and related objects.
 * 
 * @author Allejo Chris G. Velarde
 */

namespace HealthCareAbroad\AdvertisementBundle\Services;

use Doctrine\Bundle\DoctrineBundle\Registry;

use HealthCareAbroad\AdvertisementBundle\Entity\AdvertisementStatuses;

use HealthCareAbroad\AdvertisementBundle\Entity\Advertisement;

use HealthCareAbroad\AdvertisementBundle\Exception\AdvertisementFactoryException;

use HealthCareAbroad\AdvertisementBundle\Entity\AdvertisementTypes;

use Symfony\Component\DependencyInjection\ContainerInterface;

class AdvertisementFactory
{
    private $discriminatorMapping = array();
    
    /**
     * @var ContainerInterface
     */
    private $container;
    
    /**
     * @var Registry
     */
    private $doctrine;
    
    public function __construct(ContainerInterface $container=null)
    {
        $this->container = $container;
        
        $this->doctrine = $this->container->get('doctrine');
        
        $this->discriminatorMapping = AdvertisementTypes::getDiscriminatorMapping();
    }
    
    /**
     * Layer for Doctrine find by id. Apply caching here.
     * 
     * @param bigint $id
     * @return Advertisement
     */
    public function findById($id)
    {
        return $this->doctrine->getRepository('AdvertisementBundle:Advertisement')->find($id);
    }
    
    public function createInstanceByType($type)
    {
        if (!\array_key_exists($type, $this->discriminatorMapping)) {
            throw AdvertisementFactoryException::unknownDiscriminatorType($type);
        }
        $cls = $this->discriminatorMapping[$type];
        
        return new $cls; 
    }
    
    public function createAdvertisementTypeSpecificForm(Advertisement $advertisement)
    {
        $formSpecificClasses = array(
            AdvertisementTypes::FEATURED_INSTITUTION => 'form.advertisement.featuredInstitution',
            AdvertisementTypes::FEATURED_LISTING => 'form.advertisement.featuredListing',
            AdvertisementTypes::NEWS_TICKER => 'form.advertisement.newsTicker'
        );
        $classMapping = \array_flip($this->discriminatorMapping);
        $advertisementType = $classMapping[\get_class($advertisement)];
        
        if (\array_key_exists($advertisementType, $formSpecificClasses)) {
            $formName = $formSpecificClasses[$advertisementType];
        }
        else {
            // no specific form for this advertisement type, use common
            $formName = 'form.advertisement';
        }
        
        return $this->container->get($formName);
    }
    
    public function save(Advertisement $advertisement)
    {
        if (!$advertisement->getId()) {
            $advertisement->setStatus(AdvertisementStatuses::INACTIVE);
        }
        
        $em = $this->doctrine->getEntityManager();
        $em->persist($advertisement);
        $em->flush();
        
        return $advertisement;
    }
}