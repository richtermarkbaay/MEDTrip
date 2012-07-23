<?php

namespace HealthCareAbroad\ListingBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * ListingRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ListingRepository extends EntityRepository
{
	public function search($criteria = array()) {
		
		//TODO: test which one of the queries below are faster or tweak or 
		//create another one if performance is non-optimal   
		$result = $this->createQueryForBasicSearchA($criteria)->getResult(); 
		//$result = $this->createQueryForBasicSearchB($criteria)->getResult();
		
		return $result; 
	}
	
	private function createQueryForBasicSearchA($criteria) {

// 			SELECT *
// 			FROM listings l
// 			LEFT JOIN medical_procedures p ON l.medical_procedure_id = p.id
// 			LEFT JOIN medical_procedure_tags mpt ON p.id = mpt.medical_procedure_id 
// 			LEFT JOIN tags t ON mpt.tag_id = t_.id
// 			WHERE (
// 				l.title LIKE '%1%'
// 				OR l.description LIKE ''
// 				OR p.name LIKE ''
// 			)
// 			AND l.id
// 			IN (
// 				SELECT listing.id
// 				FROM listing_locations location
// 				INNER JOIN listings listing ON location.listing_id = listing.id
// 				AND location.country_id = 1 AND location.city_id = 1
// 			)
		//TODO: 
		// 1. provider id
		// 2. tags
		// 3. performance tweaks

		$searchTerm = '%'.$criteria['searchTerm'].'%';		
		$country = isset($criteria['countryId']) ? $criteria['countryId'] : null;
		$city = isset($criteria['cityId']) ? $criteria['cityId'] : null;		
		
		$dql = ' SELECT l FROM ListingBundle:Listing l LEFT JOIN l.procedure p LEFT JOIN p.tags t ';
		//look in title, description, procedure, or tag for search term
		$whereClause = ' WHERE (l.title LIKE :searchTerm OR l.description LIKE :searchTerm OR p.name LIKE :searchTerm OR t.name LIKE :searchTerm) ';

		//filter by country and/or city
		if ($country && $city) {
			$whereClause .= ' AND l.id IN (SELECT DISTINCT listing.id FROM ListingBundle:ListingLocation location JOIN location.listing listing WHERE location.country = :country AND location.city = :city) ';
		} else if ($country) {
			$whereClause .= ' AND l.id IN (SELECT DISTINCT listing.id FROM ListingBundle:ListingLocation location JOIN location.listing listing WHERE location.country = :country) ';
		} else if ($city) {
			$whereClause .= ' AND l.id IN (SELECT DISTINCT listing.id FROM ListingBundle:ListingLocation location JOIN location.listing listing WHERE location.city = :city) ';			
		}
		$dql .= $whereClause;

		$query = $this->_em->createQuery($dql);
		$query->setParameter('searchTerm', $searchTerm);
		if (isset($country)) $query->setParameter('country', $country);
		if (isset($city)) $query->setParameter('city', $city);
			
		return $query;
	}
	
	private function createQueryForBasicSearchB($criteria) {
		//TODO:
	}
}