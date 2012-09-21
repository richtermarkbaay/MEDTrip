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
		->restoreGlobalAccountsDatabaseState();
	}
	
	public function setUp()
	{
		$this->formValues = array(
				'userLogin[email]' => $this->userEmail,
				'userLogin[password]' => $this->userPassword
		);
	}
	
	protected function requestUrlWithNoLoggedInUser($uri, $method="GET")
	{
		$client = static::createClient();
		$client->request($method, $uri);
		return $client;
	}
	
	protected function getBrowserWithActualLoggedInUser($options = array())
	{
	    if (null === self::$clientWithLoggedUser)
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
}