<?php
/**
 * Service class for Advertisement
 * 
 * @author Allejo Chris G. Velarde
 *
 */
namespace HealthCareAbroad\AdvertisementBundle\Services;

use HealthCareAbroad\AdvertisementBundle\Entity\AdvertisementTypes;

use HealthCareAbroad\AdvertisementBundle\Repository\AdvertisementRepository;

use HealthCareAbroad\AdvertisementBundle\Entity\FeaturedListingAdvertisement;

use HealthCareAbroad\AdvertisementBundle\Entity\FeaturedInstitutionAdvertisement;

use HealthCareAbroad\AdvertisementBundle\Entity\NewsTickerAdvertisement;

use HealthCareAbroad\AdvertisementBundle\Services\AdvertisementFactory;

use Symfony\Component\DependencyInjection\ContainerInterface;

class AdvertisementService
{
    /**
     * @var ContainerInterface
     */
    private $container;
    
    /**
     * @var AdvertisementFactory
     */
    private $factory;
    
    /**
     * @var AdvertisementRepository
     */
    private $repository;
    
    public function __construct(ContainerInterface $container=null)
    {
        $this->container = $container;
        
        $this->repository = $this->container->get('doctrine')->getRepository('AdvertisementBundle:Advertisement');
    }
    
    /**
     * Get NewsTickerAdvertisements that are active and viewable in the frontend. Caching will be applied in this layer
     * 
     * @return array NewsTickerAdvertisement
     */
    public function getActiveNewsTickerAdvertisements()
    {
        // TODO: implement retrieving from cache
        
        $advertisements = $this->repository->getActiveAdvertisementsByType(AdvertisementTypes::NEWS_TICKER);
        
        return $advertisements;
    }
    
    /**
     * Get FeaturedInstitutionAdvertisement that are active and viewable in the frontend. Caching will be applied in this layer.
     * 
     * @return array FeaturedInstitutionAdvertisement
     */
    public function getActiveFeaturedInstitutionAdvertisements()
    {
        // TODO: implement retrieving from cache
        
        $advertisements = $this->repository->getActiveAdvertisementsByType(AdvertisementTypes::FEATURED_INSTITUTION);
        
        return $advertisements;
    }
    
    /**
     * Get FeaturedListingAdvertisement that are active and viewable in the frontend. Caching will be applied in this layer.
     * 
     * @return array FeaturedListingAdvertisement
     */
    public function getActiveFeaturedListingAdvertisements()
    {
        // TODO: implement retrieving from cache
        
        $advertisements = $this->repository->getActiveAdvertisementsByType(AdvertisementTypes::FEATURED_LISTING);
        
        return $advertisements;
    }
}