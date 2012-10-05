<?php
use HealthCareAbroad\SearchBundle\Constants;
use HealthCareAbroad\AdminBundle\Tests\AdminBundleWebTestCase;

class DefaultControllerTest extends AdminBundleWebTestCase
{
	private $searchTerms = array(
			Constants::SEARCH_CATEGORY_INSTITUTION => 'Test Institution Medical Clinic',
			Constants::SEARCH_CATEGORY_CENTER => 'AddedFromTest Center',
			Constants::SEARCH_CATEGORY_PROCEDURE_TYPE => 'Test Treatment',
			Constants::SEARCH_CATEGORY_PROCEDURE => 'Test Treatment Procedure',
	);
	
	public function setUp() {
		//parent::setUp();
		
		$this->client = $this->getBrowserWithMockLoggedUser(array('debug' => false));
		$this->crawler = $this->client->request('GET', '/admin/search/widget/admin');		
		
		//<input type="image" alt="Search" src=... />
		$this->submitButton = $this->crawler->selectButton('Search');
		 
		//this will produce an error if $submitButton does not exist
		$this->form = $this->submitButton->form();
	}
	
	public function testShowWidgetAction()
	{
		$this->assertCount(1, $this->crawler->filter('form'));
		$this->assertEquals('POST', $this->form->getMethod());
		//$this->assertRegExp('/\/admin\/search/', $this->form->getUri());
		$this->assertContains('/admin/search', $this->form->getUri());
		
		$inputElements = $this->crawler->filter('input');
		$this->assertCount(2, $inputElements);
		
		$inputText = $inputElements->eq(0);
		$this->assertEquals('text', $inputText->attr('type'));
		$this->assertEquals('adminDefaultSearch[term]', $inputText->attr('name'));
		
		$inputImage = $inputElements->eq(1);
		$this->assertEquals('image', $inputImage->attr('type'));
		
		$selectElements = $this->crawler->filter('select');
		$this->assertCount(1, $selectElements);
		
		$selectCategory = $selectElements->eq(0); 
		$this->assertEquals('adminDefaultSearch[category]', $selectCategory->attr('name'));
	}
	
	public function testShowWidgetAction_NonExistentContext()
	{
		$crawler = $this->client->request('GET', '/admin/search/widget/nonexistentContext');
		
		$this->assertEquals(500, $this->client->getResponse()->getStatusCode());
	}	
	
	public function testInitiateAction_SearchForInstitution()
	{
		$searchTerm = $this->searchTerms[Constants::SEARCH_CATEGORY_INSTITUTION];

		$this->form['adminDefaultSearch[term]'] = $searchTerm;
		$this->form['adminDefaultSearch[category]'] = Constants::SEARCH_CATEGORY_INSTITUTION;
		$this->client->submit($this->form);
		
		$response = $this->client->getResponse();
		$this->assertTrue($response->isSuccessful());
		
		$content = $response->getContent();
		//$this->assertRegExp('/'.$searchTerm.'/', $content);
		$this->assertContains($searchTerm, $content);
	}
	
	public function testInitiateAction_SearchForCenter()
	{
		$searchTerm = $this->searchTerms[Constants::SEARCH_CATEGORY_CENTER];
		
		$this->form['adminDefaultSearch[term]'] = $searchTerm;
		$this->form['adminDefaultSearch[category]'] = Constants::SEARCH_CATEGORY_CENTER;
		$this->client->submit($this->form);
	
		$response = $this->client->getResponse();
		$this->assertTrue($response->isSuccessful());
	
		$content = $response->getContent();
		//$this->assertRegExp('/'.$searchTerm.'/', $content);
		$this->assertContains($searchTerm, $content);
	}	
	
	public function testInitiateAction_SearchForProcedureType()
	{
		$searchTerm = $this->searchTerms[Constants::SEARCH_CATEGORY_PROCEDURE_TYPE];
	
		$this->form['adminDefaultSearch[term]'] = $searchTerm;
		$this->form['adminDefaultSearch[category]'] = Constants::SEARCH_CATEGORY_PROCEDURE_TYPE;
		$this->client->submit($this->form);
	
		$response = $this->client->getResponse();
		$this->assertTrue($response->isSuccessful());
	
		$content = $response->getContent();
		//$this->assertRegExp('/'.$searchTerm.'/', $content);
		$this->assertContains($searchTerm, $content);
	}	
	
	public function testInitiateAction_SearchForProcedure()
	{
		$searchTerm = $this->searchTerms[Constants::SEARCH_CATEGORY_PROCEDURE];
	
		$this->form['adminDefaultSearch[term]'] = $searchTerm;
		$this->form['adminDefaultSearch[category]'] = Constants::SEARCH_CATEGORY_PROCEDURE;
		$this->client->submit($this->form);
	
		$response = $this->client->getResponse();
		$this->assertTrue($response->isSuccessful());
	
		$content = $response->getContent();
		$this->assertContains($searchTerm, $content);
	}	
}