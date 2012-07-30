<?php
/**
 * @author Allejo Chris G. Velarde
 */
include "bootstrap.php.cache";

require_once(realpath(dirname(__DIR__).'/app/AppKernel.php'));

class HCA_PHPUnitBootstrap
{
    private static $isBooted = false;
    
    private static $kernel;
    
    private static $container;
    
    static public function boot()
    {
        if (self::$isBooted) {
            return false;
        }
        
        self::$kernel =  new \AppKernel('test', false);
        self::$kernel->boot();
        self::$container = self::$kernel->getContainer();
        
        if (!ini_get('display_errors')) {
            ini_set('display_errors', '1');
        }
        
        HCA_DatabaseManager::getInstance()->setDoctrine(self::$container->get('doctrine'))->restoreDatabaseState();
        
        
        self::$isBooted = true;
        return true;
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
    }
    
}

HCA_PHPUnitBootstrap::boot();