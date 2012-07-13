<?php
namespace HealthCareAbroad\ListingBundle\Service;

use HealthCareAbroad\ListingBundle\Entity\ListingLocation;

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
	
	public function addListing($data = array())
	{
		//$data = $this->prepareListingData($data);
		//$this->entityManager->getRepository('ListingBundle:Listing')->saveListing($data['listing']);
		//$this->entityManager->getRepository('ListingBundle:ListingLocation')->saveListing($data['listing']);

		$provider = $this->entityManager->getRepository('ProviderBundle:Provider')->findOneById($data['provider']);
		$procedure = $this->entityManager->getRepository('ProcedureBundle:MedicalProcedure')->findOneById($data['procedure']);			

		
		$listing = new Listing();
		$listing->setTitle($data['title']);
		$listing->setDescription($data['description']);
		//$listing->setLogo('testlogo');
		$listing->setDateModified(new \DateTime("now"));
		$listing->setDateCreated(new \DateTime("now"));
		$listing->setStatus($data['status']);
		$listing->setProvider($provider);
		$listing->setProcedure($procedure);
		$this->entityManager->persist($listing);
		$this->entityManager->flush($listing);


		$country = $this->entityManager->getRepository('HelperBundle:Country')->findOneById($data['country']);
		$city = $this->entityManager->getRepository('HelperBundle:City')->findOneById($data['city']);
		
		$listingLocation = new ListingLocation();
		$listingLocation->setListing($listing);
		$listingLocation->setCountry($country);
		$listingLocation->setCity($city);
		$listingLocation->setAddress($data['address']);
		$this->entityManager->persist($listingLocation);
		$this->entityManager->flush($listingLocation);
		
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