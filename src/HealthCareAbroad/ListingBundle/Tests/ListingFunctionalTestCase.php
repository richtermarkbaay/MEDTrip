<?php
namespace HealthCareAbroad\ListingBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * TODO:
 * Transform this into a more generic class for use with tests needing database
 * access
 * 
 */
abstract class ListingFunctionalTestCase extends WebTestCase
{
	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	protected $em;	
	
	public function setUp()
	{
		$kernel = static::createKernel();
		$kernel->boot();
		$this->em = $kernel->getContainer()->get('doctrine.orm.entity_manager');
		
		$this->initDatabase();		
	}
	
	public function tearDown()
	{
		$kernel->getContainer()->get('doctrine')->getConnection()->close();
		//parent::tearDown();		
	}
	
	protected function initDatabase()
	{
		$connection = $this->em->getConnection();
		$databaseName = $connection->getDatabase();
		
		// just in case
		if ($databaseName != 'fixtures_healthcareabroad'){
			throw new \Exception("You must use `fixtures_healthcareabroad` database for testing instead of `{$databaseName}`");
		}
		
		$connection->getSchemaManager()->dropAndCreateDatabase($databaseName);
		$connection->exec("USE `{$databaseName}`");
		$fixturesSqlFile = realpath(dirname(__DIR__).'/../../../data/fixtures_healthcareabroad.sql');
		
		if (!\is_file($fixturesSqlFile)) {
			throw new \Exception('File does not exist: '.$fixturesSqlFile);
		}
		
		$stmt = $connection->prepare(file_get_contents($fixturesSqlFile));
		$r = $stmt->execute();
	}
	
	public static function setUpBeforeClass()
	{
		if (!ini_get('display_errors')) {
			ini_set('display_errors', '1');
		}
	}
	
	public static function tearDownAfterClass()
	{
	}	
}