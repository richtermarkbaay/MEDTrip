<?php
namespace HealthCareAbroad\InstitutionBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use \HCA_DatabaseManager;

abstract class InstitutionBundleWebTestCase extends WebTestCase
{
    protected $userEmail = 'test.user@chromedia.com';
    protected $userPassword = '123456';
    protected $formValues = array();
    
    protected $loginAbsoluteUri = 'http://localhost/institution/login';
    protected $loginRelativeUri = '/institution/login';
   
    protected $doctrine;
    static protected $clientWithLoggedUser=null; 
    
    public static function setUpBeforeClass()
    {
        \HCA_DatabaseManager::getInstance()
        ->restoreDatabaseState()
        ->restoreGlobalAccountsDatabaseState();
    }

    public function setUp()
    {
        $this->formValues = array(
            'userLogin[email]' => $this->userEmail,
            'userLogin[password]' => $this->userPassword
        );
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
    
    protected function requestUrlWithNoLoggedInUser($uri, $method="GET")
    {
        $client = static::createClient();
        $client->request($method, $uri);
        return $client;
    }
    
    protected function getBrowserWithActualLoggedInUser()
    {
        //if (self::$clientWithLoggedUser === null) {
            self::$clientWithLoggedUser = static::createClient();
            $crawler = self::$clientWithLoggedUser->request('GET', '/institution/login');
            
            $form = $crawler->selectButton('submit')->form();
            self::$clientWithLoggedUser->submit($form, $this->formValues);
        //}
        
        return self::$clientWithLoggedUser;
    }
    
    protected function getBrowserWithMockLoggedUser()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'developer',
            'PHP_AUTH_PW'   => '123456',
        ));
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
    
    /**
     * Convenienve function to check if location header is the login page. 
     * 
     * @param unknown_type $client
     */
    protected function isRedirectedToLoginPage($client)
    {
        $location = $this->getLocationResponseHeader($client);
        return $location == '/institution/login' || $location == 'http://localhost/institution/login';
    }
    
    /**
     * Convenience function to set an invalid institution id in the browser session
     * @param unknown_type $client
     */
    protected function setInvalidInstitutionInSession(&$client)
    {
        $session = $client->getContainer()->get('session');
        $session->set('institutionId', 99999999);
        $session->save();
    }
}