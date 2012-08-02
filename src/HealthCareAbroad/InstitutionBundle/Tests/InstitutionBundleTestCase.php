<?php


namespace HealthCareAbroad\HelperBundle\Tests;

use Symfony\Component\Console\Helper\HelperSet;

use Doctrine\ORM\Tools\Console\ConsoleRunner;

require_once realpath(dirname(__DIR__).'/../../../app/AppKernel.php');

abstract class HelperBundleTestCase extends \PHPUnit_Framework_TestCase
{
    protected static $kernel;
    
    protected static $container;
    
    public static function setUpBeforeClass()
    {
        self::$kernel =  new \AppKernel('test', false);
        self::$kernel->boot();
        self::$container = self::$kernel->getContainer();
        
        if (!ini_get('display_errors')) {
            ini_set('display_errors', '1');
        }
        self::restoreDatabaseState();
    }
    
    public static function tearDownAfterClass()
    {
        self::restoreDatabaseState();
    }
    
    protected static function restoreDatabaseState()
    {
        
        $doctrine = self::$container->get('doctrine');
        $connection = $doctrine->getConnection();
        $databaseName = $connection->getDatabase();
        echo $databaseName;exit;
        // extra check that we are indeed using fixtures_healthcareabroad database
        if ($databaseName != 'fixtures_healthcareabroad'){
            throw new \Exception("You must use `fixtures_healthcareabroad` database for testing instead of `{$databaseName}`");
        }
        
        // drop and create fixtures db
        $connection->getSchemaManager()->dropAndCreateDatabase($databaseName);
        
        $connection->exec("USE `{$databaseName}`");
        $fixturesSqlFile = realpath(dirname(__DIR__).'/../../../data/healthcareabroad.sql');
        
        // import fixtures dump
        $sql = file_get_contents($fixturesSqlFile);
        $stmt = $connection->prepare($sql);
        $r = $stmt->execute();
    }
}