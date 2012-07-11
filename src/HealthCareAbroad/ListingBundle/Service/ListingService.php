<?php
namespace HealthCareAbroad\ListingBundle\Service;

use HealthCareAbroad\ListingBundle\Entity\Listing;
use HealthCareAbroad\ListingBundle\Repository\ListingRepository;
use HealthCareAbroad\ListingBundle\Service\ListingData;
use HealthCareAbroad\ProviderBundle\Entity\Provider;
use Doctrine\Common\Collections\ArrayCollection;

use Doctrine\ORM\EntityManager;

class ListingService
{
	protected $entityManager;
	
	public function __construct(EntityManager $entityManager)
	{
		$this->entityManager = $entityManager;
	}
	
	public function getListing($id)
	{
		//$repository = $this->entityManager->getRepository('HealthCareAbroadBundle:Listing');
		$repository = $this->entityManager->getRepository('ListingBundle:Listing');
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
	
	public function getListings($providerId)
	{
		$provider = $this->entityManager->getRepository('ProviderBundle:Provider')->findOneById($providerId);
		
		$listings = $this->entityManager->getRepository('ListingBundle:Listing')->findByProvider($provider);
		
		return $listings;
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
		$provider = $this->entityManager->getRepository('ProviderBundle:Provider')->findOneById($data->get('provider_id'));
		
		$listing = new Listing();
		$listing->setTitle($data->get('title'));
		$listing->setDescription($data->get('description'));
		$listing->setDateModified(new \DateTime("now"));
		$listing->setDateCreated(new \DateTime("now"));
		$listing->setStatus($data->get('status'));
		$listing->setProvider($provider);
		$this->entityManager->persist($listing);
		$this->entityManager->flush($listing);
		
		return $listing;
	}
	
	public function editListing(ListingData $data)
	{
		$listing = $this->entityManager->getRepository('ListingBundle:Listing')->find($data->get('id'));
		$listing->setTitle($data->get('title'));
		$listing->setDescription($data->get('description'));
		$listing->setDateModified(new \DateTime("now"));
		$listing->setStatus($data->get('status'));
		//$listing->setProvider($provider);

		$this->entityManager->flush($listing);		
		
		return $listing;
	}
	
	public function deleteListing(ListingData $data)
	{
		$listing = $this->entityManager->getRepository('ListingBundle:Listing')->find($data->get('id'));

		$this->entityManager->remove($listing);
		$this->entityManager->flush($listing);
	}
}