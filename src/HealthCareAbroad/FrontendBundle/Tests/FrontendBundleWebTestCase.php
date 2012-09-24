<?php
namespace HealthCareAbroad\FrontendBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use \HCA_DatabaseManager;

abstract class FrontendBundleWebTestCase extends WebTestCase
{
    public static function setUpBeforeClass()
    {
        \HCA_DatabaseManager::getInstance()
        ->restoreDatabaseState()
        ->restoreGlobalAccountsDatabaseState();
    }
    
    /**
     * Convenience function to get location response headers
     *
     * @param unknown_type $client
     */
    protected function getLocationResponseHeader($client)
    {
    	return $client->getResponse()->headers->get('location');
    }
}