<?php

namespace HealthCareAbroad\UserBundle\Tests;

use \HCA_DatabaseManager;

abstract class UserBundleTestCase extends \PHPUnit_Framework_TestCase
{
    private $doctrine = null;
    
    private $container = null;
    
    public static function setUpBeforeClass()
    {
        \HCA_DatabaseManager::getInstance()->restoreDatabaseState();
    }
    
    public static function tearDownAfterClass()
    {
        \HCA_DatabaseManager::getInstance()->restoreDatabaseState();
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