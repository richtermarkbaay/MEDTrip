<?php
namespace HealthCareAbroad\AdminBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use \HCA_DatabaseManager;

abstract class InstitutionBundleWebTestCase extends WebTestCase
{
    public static function setUpBeforeClass()
    {
        \HCA_DatabaseManager::getInstance()
        ->restoreDatabaseState()
        ->restoreGlobalAccountsDatabaseState();
    }
    
    protected function requestUrlWithNoLoggedInUser($uri, $method="GET")
    {
        $client = static::createClient();
        $client->request($method, $uri);
        return $client;
    }
    
    protected function getBrowserWithActualLoggedInUser()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/institution/login');
        
        $form = $crawler->selectButton('submit')->form();
        $client->submit($form, $this->formValues);
        return $client;
        
    }
    
    protected function getBrowserWithMockLoggedUser()
    {
        $client = static::createClient(array(), array(
                        'PHP_AUTH_USER' => 'ryan',
                        'PHP_AUTH_PW'   => 'ryanpass',
        ));
    }
}