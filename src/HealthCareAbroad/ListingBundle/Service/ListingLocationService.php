<?php
namespace HealthCareAbroad\ListingBundle\Service;

use Doctrine\ORM\EntityManager;
use HealthCareAbroad\ListingBundle\Entity\ListingLocation;
use HealthCareAbroad\ListingBundle\Entity\Listing;

class ListingLocationService
{
	protected $entityManager;
	
	public function __construct(EntityManager $entityManager)
	{
		$this->entityManager = $entityManager;
	}

	public function getLocation($listingLocationId) 
	{
		return $this->entityManager->getRepository('ListingBundle:ListingLocation')->find($listingLocationId);		
	}

	/**
	 * As of know we will assume that we only have one location per listing
	 * @param int $listingId
	 */	
	public function getLocationByListingId($listingId) 
	{
		return $this->getLocationByListing($this->entityManager->find('ListingBundle:Listing', $listingId));
	}
	
	public function getLocationByListing(Listing $entity)
	{
		return $this->entityManager->getRepository('ListingBundle:ListingLocation')->findOneBy(array(
				'listing' => $entity
		));		
	}
	
	/**
	 * "Raw data" simply means that instead of passing in entities we send their IDs instead.
	 * @param unknown_type $data
	 */
	public function addLocation($rawData) 
	{
		$entity = $this->normalizeData($rawData);
		
		return $this->saveLocation($entity);  
	}
	
	public function editLocation($rawData)
	{
		$entity = $this->normalizeData($rawData);
		
		return $this->saveLocation($entity);  	
	}	
	
	public function saveLocation(ListingLocation $entity) 
	{
		$this->entityManager->persist($entity);
		$this->entityManager->flush($entity);
		
		return $entity;
	}
	
	private function normalizeData($data)
	{
		$normalizedData = array();
		
		foreach ($data as $key => $value)
		{
			if ("city" == $key) 
			{
				$city = $this->entityManager->getRepository("HelperBundle:City")->find($value);
				$normalizedData[$key] = $city;
			} 
			else if ("country" == $key) 
			{
				$country = $this->entityManager->getRepository("HelperBundle:Country")->find($value);
				$normalizedData[$key] = $country;
			} 
			else if ("listing" == $key)
			{
				$listing = $this->entityManager->getRepository("ListingBundle:Listing")->find($value);
				$normalizedData[$key] = $listing;					 	
			} 
			else {
				$normalizedData[$key] = $value;
			}
		}

		if (isset($normalizedData['id']) && $normalizedData['id'])
		{
			$entity = $this->entityManager->find("ListingBundle:ListingLocation", $normalizedData['id']);	
		} 
		else 
		{
			$entity = new ListingLocation();
		}
			
		$entity->setCity($data['city']);
		$entity->setCountry($data['country']);
		$entity->setListing($data['listing']);
		$entity->setAddress($data['address']);		
		
		return $entity;
	}
	
	public function deleteLocationById($listingLocationId) {
		$location = $this->entityManager->find('ListingBundle:ListingLocation', $listingLocationId);
		
		$this->deleteLocation($location);
	}
	
	public function deleteLocation(ListingLocation $entity) {
		if (!$entity) {
			throw new \Exception('Invalid entity');
		}		
		
		$this->entityManager->remove($entity);
		$this->entityManager->flush($entity);
	}
}
