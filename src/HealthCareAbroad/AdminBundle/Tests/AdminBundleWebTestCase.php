<?php
namespace HealthCareAbroad\AdminBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use \HCA_DatabaseManager;

abstract class AdminBundleWebTestCase extends WebTestCase
{
	protected $doctrine;
	
	protected $userEmail = 'test.adminuser@chromedia.com';
	protected $userPassword = '123456';
	protected $formValues = array();
	
	private $defaultClientOptions = array(
			'environment'	=> 'test',
			'debug'			=> true,
	);
	
	private static $clientWithLoggedUser = null;
	
	public static function setUpBeforeClass()
	{
		\HCA_DatabaseManager::getInstance()
		->restoreDatabaseState()
		//->restoreGlobalAccountsDatabaseState()
		;
	}
	
	public function setUp()
	{
		$this->formValues = array(
				'userLogin[email]' => $this->userEmail,
				'userLogin[password]' => $this->userPassword
		);
	}
	
	protected function getBrowserWithActualLoggedInUser($options = array())
	{
	    $freshLogin = \array_key_exists('freshLogin', $options) && $options['freshLogin'];
	    
	    if (null === self::$clientWithLoggedUser || $freshLogin)
	    {
	        self::$clientWithLoggedUser = static::createClient(\array_merge($this->defaultClientOptions, $options));
	        $crawler = self::$clientWithLoggedUser->request('GET', '/admin/login');
	        $form = $crawler->selectButton('submit')->form();
	        self::$clientWithLoggedUser->submit($form, $this->formValues);
	    }
		

		return self::$clientWithLoggedUser;
	}
	
	protected function getBrowserWithMockLoggedUser($options = array())
	{
		$client = static::createClient(\array_merge($this->defaultClientOptions, $options), array(
				'PHP_AUTH_USER' => 'developer',
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
}