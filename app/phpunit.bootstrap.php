<?php
use Guzzle\Http\Client;

use HealthCareAbroad\HelperBundle\Services\AlertService;

use HealthCareAbroad\HelperBundle\Classes\CouchDatabase;

/**
 * @author Allejo Chris G. Velarde
 */
include "bootstrap.php.cache";

require_once(realpath(dirname(__DIR__).'/app/AppKernel.php'));

class HCA_PHPUnitBootstrap
{
    private static $isBooted = false;
    
    private static $kernel;
    
    static public function boot()
    {
        if (self::$isBooted) {
            return false;
        }
        
        self::$kernel =  new \AppKernel('test', false);
        self::$kernel->boot();
        
        if (!ini_get('display_errors')) {
            ini_set('display_errors', '1');
        }
        
        HCA_ServiceManager::getInstance()->setContainer(self::$kernel->getContainer());
        
        // init database state
        HCA_DatabaseManager::getInstance()->setDoctrine(HCA_ServiceManager::getInstance()->getContainer()->get('doctrine'))->restoreDatabaseState();
        
        
        self::$isBooted = true;
        return true;
    }
}

class HCA_ServiceManager
{
    private static $instance;
    
    private $container;
    
    private function __construct()
    {

    }
    
    /**
     * @return HCA_ServiceManager
     */
    static public function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new HCA_ServiceManager;
        }
        return self::$instance;
    }
    
    public function setContainer($container)
    {
        $this->container = $container;
        return $this;
    }
    
    public function getContainer()
    {
        return $this->container;
    }
}


class HCA_DatabaseManager
{
    private static $instance;
    
    private $doctrine;
    
    private function __construct()
    {
        
    }
    
    /**
     * @return HCA_DatabaseManager
     */
    static public function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new HCA_DatabaseManager;
        }
        return self::$instance;
    }
    
    /**
     * 
     * @param Doctrine\Bundle\DoctrineBundle\Registry $doctrine
     * @return HCA_DatabaseManager
     */
    public function setDoctrine(Doctrine\Bundle\DoctrineBundle\Registry $doctrine)
    {
        $this->doctrine = $doctrine;
        return $this;
    }
    
    /**
     * 
     * @return \Doctrine\Bundle\DoctrineBundle\Registry
     */
    public function getDoctrine()
    {
        return $this->doctrine;
    }
    
    public function restoreDatabaseState()
    {
        $connection = $this->doctrine->getConnection();
        $databaseName = $connection->getDatabase();
        
        // extra check that we are indeed using fixtures_healthcareabroad database
        if ($databaseName != 'fixtures_healthcareabroad'){
            throw new \Exception("You must use `fixtures_healthcareabroad` database for testing instead of `{$databaseName}`");
        }
        
        // drop and create fixtures db
        $connection->getSchemaManager()->dropAndCreateDatabase($databaseName);
        
        $connection->exec("USE `{$databaseName}`");
        $fixturesSqlFile = realpath(dirname(__DIR__).'/data/fixtures_healthcareabroad.sql');
        
        //import fixtures dump
        $sql = file_get_contents($fixturesSqlFile);
        $stmt = $connection->prepare($sql);
        $r = $stmt->execute();
        
        return $this;
    }
    
    public function restoreAlertCouchDbState()
    {
        $baseUrl = HCA_ServiceManager::getInstance()->getContainer()->getParameter('couchDbBaseUrl');
        $couchDbAlert = HCA_ServiceManager::getInstance()->getContainer()->getParameter('couchDbAlert');

        if ($couchDbAlert != 'fixtures_alerts'){
            throw new \Exception("You must use `fixtures_alerts` couch database for testing instead of `$couchDbAlert`");
        }

        try {
            $client = new Client($baseUrl);
            $response = $client->delete($couchDbAlert)->send();            
        } catch(\Exception $e) {
            //echo $e->getMessage();
        }

        return $this;
    }

    public function restoreGlobalAccountsDatabaseState()
    {
        $connection = $this->doctrine->getConnection();
        $databaseName = 'fixtures_chromedia_global';
        
        // drop and create fixtures db
        $connection->getSchemaManager()->dropAndCreateDatabase($databaseName);
        $connection->exec("USE `{$databaseName}`");
        $fixturesSqlFile = realpath(dirname(__DIR__).'/data/fixtures_chromedia_global.sql');
        $sql = file_get_contents($fixturesSqlFile);
        $stmt = $connection->prepare($sql);
        $r = $stmt->execute();
        $connection->close(); // close the connection
        
        // reconnect
        $connection->connect();
        
        return $this;
    }
    
}

HCA_PHPUnitBootstrap::boot();