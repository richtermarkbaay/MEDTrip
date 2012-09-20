<?php
namespace HealthCareAbroad\AdvertisementBundle\Services;

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
    
    public function __construct(ContainerInterface $container=null)
    {
        $this->container = $container;
        $this->discriminatorMapping = array(
            AdvertisementTypes::NEWS_TICKER => 'HealthCareAbroad\AdvertisementBundle\Entity\NewsTickerAdvertisement:',
            AdvertisementTypes::FEATURED_INSTITUTION => 'HealthCareAbroad\AdvertisementBundle\Entity\FeaturedInstitutionAdvertisement',
            AdvertisementTypes::FEATURED_LISTING => 'HealthCareAbroad\AdvertisementBundle\Entity\FeaturedListingAdvertisement'
        );
    }
    
    public function createInstanceByType($type)
    {
        if (!\array_key_exists($type, $this->discriminatorMapping)) {
            throw AdvertisementFactoryException::unknownDiscriminatorType($type);
        }
        $cls = $this->discriminatorMapping[$type];
        
        return new $cls; 
    }
}