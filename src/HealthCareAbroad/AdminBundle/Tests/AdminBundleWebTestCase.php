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
	
	protected function getBrowserWithActualLoggedInUser($options = array())
	{
		$client = static::createClient(\array_merge($this->defaultClientOptions, $options));
		$crawler = $client->request('GET', '/admin/login');
		$form = $crawler->selectButton('submit')->form();
		$client->submit($form, $this->formValues);

		return $client;
	}
	
	protected function getBrowserWithMockLoggedUser($options = array())
	{
		$client = static::createClient(\array_merge($this->defaultClientOptions, $options), array(
				'PHP_AUTH_USER' => 'admin',
				'PHP_AUTH_PW'   => 'testadmin',
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