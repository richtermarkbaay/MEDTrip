<?php
namespace HealthCareAbroad\ListingBundle\Tests\Repository;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use Doctrine\ORM\EntityRepository;
use HealthCareAbroad\ListingBundle\Tests\ListingFunctionalTestCase;

class ListingRepositoryTest extends ListingFunctionalTestCase
{
	public function testSearch()
	{
		$results = $this->em
			->getRepository('ListingBundle:Listing')
			->search(array('searchTerm' => '1', 'country' => '', 'city' => '')
		);
	
		$this->assertCount(2, $results);
	}
}