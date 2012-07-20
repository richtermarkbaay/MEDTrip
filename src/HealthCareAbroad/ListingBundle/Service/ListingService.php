<?php
namespace HealthCareAbroad\ListingBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;

use HealthCareAbroad\ListingBundle\Entity\Listing;
use HealthCareAbroad\ListingBundle\Repository\ListingRepository;
use HealthCareAbroad\ListingBundle\Service\ListingData;
use HealthCareAbroad\ProviderBundle\Entity\Provider;
use Doctrine\Common\Collections\ArrayCollection;

use Doctrine\ORM\EntityManager;

class ListingService
{
	private $container;
	private $entityManager;

	public function setContainer(ContainerInterface $container = null) {
		$this->container = $container;
		$this->entityManager = $this->container->get('doctrine')->getEntityManager();
	}

	public function getListing($id)
	{
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
		
		$criteria = array('provider' => $provider, 'status' => 1);
		$listings = $this->entityManager->getRepository('ListingBundle:Listing')->findBy($criteria);
		
		return $listings;
	}
	
	/**
	 * initial searchable fields: procedure/title, provider, location, description
	 * 
	 * @param unknown_type $searchCriteria
	 */
	public function searchListings($searchCriteria) {
	}
	
	private function prepareListingData($data) {
		$location = array(
			'country_id' => $data['country'], 
			'city_id' => $data['city'], 
			'address' => $data['address']
		);

		$listing = array(
			'provider_id' => $data['provider'],
			'medical_procedure_id' => $data['procedure'],
			'title' => $data['title'],
			'description' => $data['description'],
			//'logo' => $data['logo']
			'status' => isset($data['status']) ? $data['status'] : 0
		);

		return array('location'=>$location, 'listing'=>$listing);
	}
	
	public function saveListing(Listing $listing)
	{
		if(!$listing->getId())
			$listing->setDateCreated(new \DateTime("now"));

		$listing->setStatus(1);
		$listing->setDateModified(new \DateTime("now"));
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
	
	public function deleteListing($id)
	{
		$listing = $this->entityManager->getRepository('ListingBundle:Listing')->find($id);
		$listing->setStatus(0);
		$this->entityManager->persist($listing);
		$this->entityManager->flush($listing);
	}
	
	public function populateLocations(Listing $listing) 
	{
		$locations = $this->container->get('services.listing_location')->getLocationByListing($listing);
		foreach($locations as $each) {
			$listing->getLocations()->add($each);
		}
		
		return $listing;
	}
}