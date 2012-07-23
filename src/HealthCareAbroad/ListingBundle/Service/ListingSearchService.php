<?php
namespace HealthCareAbroad\ListingBundle\Service;

use Doctrine\ORM\EntityManager;

use HealthCareAbroad\ListingBundle\Entity\Listing;
use HealthCareAbroad\ListingBundle\Entity\ListingLocation;
use HealthCareAbroad\HelperBundle\Entity\City;
use HealthCareAbroad\HelperBundle\Entity\Country;

/**
 * TODO: optimize searches
 * 
 * @author harold
 */
class ListingSearchService
{
	private $entityManager;
	
	public function __construct(EntityManager $entityManager)
	{
		$this->entityManager = $entityManager;
	}
	
	/**
	 * @param array $searchCriteria
	 */
	public function getListings($criteria = array()) 
	{
		$repository = $this->entityManager->getRepository('ListingBundle:Listing');
		$listings = $repository->search($criteria);
		
		return $listings;
	}
	
	// Common criteria:
	// TODO: use transactions
	private function getListingsByCountry($criteria = array())
	{
		$listings = array();
		return $listings;
	}
	private function getListingsByCity($criteria = array())
	{
		$listings = array();
		return $listings;		
	}	
}