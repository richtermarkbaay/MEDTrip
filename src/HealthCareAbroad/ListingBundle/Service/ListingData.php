<?php
namespace HealthCareAbroad\ListingBundle\Service;

use Doctrine\Common\Collections\ArrayCollection;

class ListingData extends ArrayCollection
{
	
	private $keys = array(
		"id",
		"procedure_id",	
		"provider_id",
		"location_id",	
		"title",
		"description",
		"status",
		
	);
	
	/**
	 * (non-PHPdoc)
	 * @see Doctrine\Common\Collections.ArrayCollection::get()
	 */
	public function get($key)
	{
		if ($this->isValidKey($key)){
			return parent::get($key);
		}
	}
	
	/**
	 * (non-PHPdoc)
	 * @see Doctrine\Common\Collections.ArrayCollection::set()
	 */
	public function set($key, $value) {
		if ($this->isValidKey($key))
		{
			parent::set($key, $value);
		}	
	}

	//Do we really need to verify the keys?
	private function isValidKey($key) {
		if (!\in_array($key, $this->keys)) {
			throw new \Exception('Invalid key: '.$key);
		}			
		
		return true;
	}
}