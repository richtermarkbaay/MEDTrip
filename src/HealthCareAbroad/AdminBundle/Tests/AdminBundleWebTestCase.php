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
	
	public static function setUpBeforeClass()
	{
		\HCA_DatabaseManager::getInstance()
		->restoreDatabaseState()
		->restoreGlobalAccountsDatabaseState();
	}
	
	public function setUp()
	{
		$this->formValues = array(
				'form[email]' => $this->userEmail,
				'form[password]' => $this->userPassword
		);
	}
	
	protected function getBrowserWithActualLoggedInUser()
	{
		$client = static::createClient();
		$crawler = $client->request('GET', '/admin/login');
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