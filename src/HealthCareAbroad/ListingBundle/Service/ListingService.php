<?php
namespace HealthCareAbroad\ListingBundle\Service;

use HealthCareAbroad\ListingBundle\Entity\Listing;
use HealthCareAbroad\ListingBundle\Repository\ListingRepository;
use HealthCareAbroad\ListingBundle\Service\ListingData;
use Doctrine\Common\Collections\ArrayCollection;

use Doctrine\ORM\EntityManager;

class ListingService
{
	protected $entityManager;
	
	public function __construct(EntityManager $entityManager)
	{
		$this->entityManager = $entityManager;
	}
	
	public function getListing($listingId)
	{
		$repository = $this->entityManager->getRepository('HealthCareAbroadBundle:Listing');
		$listing = $repository->findOneById($id); 
		
		return $listing;
	}
	
	/**
	 * @returns array 
	 * @param unknown_type $listingId
	 */
	public function getListingProperties($listingId) 
	{
	}
	
	public function getFullListing($listingId) 
	{
	}
	
	public function getListingsByProviderId($providerId, $criteria)
	{
	}
	
	public function getListings($criteria)
	{
	}
	
	/**
	 * initial searchable fields: procedure/title, provider, location, description
	 * 
	 * @param unknown_type $searchCriteria
	 */
	public function searchListings($searchCriteria) {
	}
	
	public function addListing(ListingData $data)
	{
		//$provider = $this->entityManager->getRepository('HealthCareAbroadBundle:Provider')->find($providerId);
		
		$msg = 'title: '.$data->get('title'). 'desc: '.$data->get('description');
		
		var_dump($msg); exit;
		
		$listing = new Listing();
		$listing->setTitle($data->get('title'));
		$listing->setDescription($data->get('description'));
		$listing->setStatus($data->get('status'));
		//$listing->setProvider($provider);
		
		$this->entityManager->persist($listing);
		$this->entityManager->flush($listing);
		
		return $listing;
	}
	
	public function editListing($providerId, ListingData $data)
	{
	}
	
	public function deleteListing($providerId)
	{
	}
}