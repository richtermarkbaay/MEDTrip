<?php
namespace HealthCareAbroad\AdminBundle\Tests;

use HealthCareAbroad\UserBundle\Entity\AdminUser;

use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use \HCA_DatabaseManager;

abstract class AdminBundleWebTestCase extends WebTestCase
{
	protected $doctrine;
	
	protected $userEmail = 'test.adminuser@chromedia.com';
	protected $userPassword = '123456';
	
	protected $normalUserEmail = 'test.anotheradminuser@chromedia.com';
	protected $normalUserPassword = '123456';
	
	protected $formValues = array();
	
	private $defaultClientOptions = array(
			'environment'	=> 'test',
			'debug'			=> true,
	);
	
	private static $clientWithLoggedUser = null;
	
	private static $clientWithNormalLoggedUser = null;
	
	private static $validToken = null;
	
	public static function setUpBeforeClass()
	{
		\HCA_DatabaseManager::getInstance()
		->restoreDatabaseState()
		//->restoreAlertCouchDbState()
		->restoreGlobalAccountsDatabaseState()
		;
	}
	
	public function setUp()
	{
		$this->formValues = array(
// 				'userLogin[email]' => $this->userEmail,
// 				'userLogin[password]' => $this->userPassword
		    '_username' => $this->userEmail,
            '_password' => $this->userPassword
		);	
	}
	
	protected function getBrowserWithActualLoggedInUser($options = array())
	{
// 	    $freshLogin = \array_key_exists('freshLogin', $options) && $options['freshLogin'];
	    
// 	    if (null === self::$clientWithLoggedUser )
// 	    {
// 	        self::$clientWithLoggedUser = static::createClient(\array_merge($this->defaultClientOptions, $options));
// 	        $crawler = self::$clientWithLoggedUser->request('GET', '/admin/login');
// 	        $form = $crawler->selectButton('submit')->form();
// 	        self::$clientWithLoggedUser->submit($form, $this->formValues);
	        
// 	        echo "\n======= Logging in {$this->userEmail}::{$this->userPassword}\n\n";
// 	    }
	    
// 	    return self::$clientWithLoggedUser;

	    $client = static::createClient(\array_merge($this->defaultClientOptions, $options), array(
	                    'PHP_AUTH_USER' => 'admin_authorized',
	                    'PHP_AUTH_PW'   => '123456',
	    ));
	    $session = $client->getContainer()->get('session');
	    $session->set('accountId', 2);
	    $session->save();
	    return $client;
	    
	}
	
	protected function getBrowserWithMockLoggedUser($options = array())
	{

// 	    if (null === self::$clientWithNormalLoggedUser)
// 	    {
	         
// 	        self::$clientWithNormalLoggedUser = static::createClient(\array_merge($this->defaultClientOptions, $options));
// 	        $crawler = self::$clientWithNormalLoggedUser->request('GET', '/admin/login');
// 	        $form = $crawler->selectButton('submit')->form();
// 	        self::$clientWithNormalLoggedUser->submit($form, array(
// 				'userLogin[email]' => $this->normalUserEmail,
// 				'userLogin[password]' => $this->normalUserPassword
// 		    ));
// 	    }
	     
// 	    return self::$clientWithNormalLoggedUser;

// 	    if (null !== self::$clientWithLoggedUser ) {
// 	        self::$clientWithLoggedUser = null;
// 	    }
	    $client = static::createClient(\array_merge($this->defaultClientOptions, $options), array(
            'PHP_AUTH_USER' => 'admin_not_authorized',
            'PHP_AUTH_PW'   => '123456',
	    ));
	    return $client;
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
	
	/**
	 * Convenienve function to request URI with no logged user
	 * 
	 * @param string $uri
	 * @param string $method
	 * @return client
	 */
	protected function requestUrlWithNoLoggedInUser($uri, $method="GET")
	{
	    $client = static::createClient();
	    $client->request($method, $uri);
	    return $client;
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
	    return $location == '/admin/login' || $location == 'http://localhost/admin/login';
	}
	
	protected function commonTestForInvalidMethodRequests($client, $uri, $invalidMethods=array())
	{
	    foreach ($invalidMethods as $method) {
	        $client->request($method, $uri);
	        $this->assertEquals(405, $client->getResponse()->getStatusCode());
	    }
	}
}