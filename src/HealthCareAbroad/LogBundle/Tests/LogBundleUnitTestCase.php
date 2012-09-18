<?php

namespace HealthCareAbroad\LogBundle\Tests;

use Doctrine\Bundle\DoctrineBundle\Registry;

use \HCA_DatabaseManager;

abstract class LogBundleUnitTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Registry
     */
    protected $doctrine = null;
    
    protected $container = null;
    
    public static function setUpBeforeClass()
    {
        \HCA_DatabaseManager::getInstance()
        ->restoreDatabaseState()
        ->restoreGlobalAccountsDatabaseState();
    }
    
    public static function tearDownAfterClass()
    {
         \HCA_DatabaseManager::getInstance()
         ->restoreDatabaseState()
         ->restoreGlobalAccountsDatabaseState();
    }
    
    /**
     * @return \Doctrine\Bundle\DoctrineBundle\Registry
     */
    public function getDoctrine()
    {
        if (\is_null($this->doctrine)) {
            $this->doctrine = HCA_DatabaseManager::getInstance()->getDoctrine();
        }
        return $this->doctrine;
    }
    
    public function getServiceContainer()
    {
        if (\is_null($this->container)) {
            $this->container = \HCA_ServiceManager::getInstance()->getContainer();
        }
        return $this->container;
    }
}