<?php


namespace HealthCareAbroad\UserBundle\Tests;

abstract class UserBundleTestCase extends \PHPUnit_Framework_TestCase
{
    protected static $kernel;
    
    protected static $container;
    
    public static function setUpBeforeClass()
    {
        \HCA_DatabaseManager::getInstance()->restoreDatabaseState();
    }
    
    public static function tearDownAfterClass()
    {
        \HCA_DatabaseManager::getInstance()->restoreDatabaseState();
    }
}