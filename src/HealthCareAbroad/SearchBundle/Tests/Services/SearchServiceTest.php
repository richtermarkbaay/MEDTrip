<?php
namespace HealthCareAbroad\SearchBundle\Tests\Services;

use HealthCareAbroad\SearchBundle\Constants;
use HealthCareAbroad\SearchBundle\Tests\ContainerAwareUnitTestCase;
use HealthCareAbroad\InstitutionBundle\Entity\Institution;
use HealthCareAbroad\MedicalProcedureBundle\Entity\ProcedureType;
use HealthCareAbroad\MedicalProcedureBundle\Entity\Procedure;

class SearchServiceTest extends ContainerAwareUnitTestCase
{
	public function setUp()
	{
		//TODO: setup database; this is as of now dependent on the test fixtures set up by another test suite
		$this->service = $this->get('services.search');
		$this->time = \time();
	}
	
	public function tearDown()
	{
	}
	
	public function testInitiateShouldReturnArrayOfInstitutionObjects()
	{
		$term = 'Test Institution Medical Clinic'; 
		$actual = $this->service->initiate(array(
				'term' => $term, 
				'category' => Constants::SEARCH_CATEGORY_INSTITUTION
		));
		$this->assertTrue(is_array($actual), 'Method initiate() should return an array');
		$this->assertNotEmpty($actual, "Searched for \"$term\"");
		$this->assertInstanceOf(
				'HealthCareAbroad\\InstitutionBundle\\Entity\\Institution', $actual[0], 
				'Method initiate() should return an array of Institution objects');

		$term = 'Test INSTItution mediCal Clinic';
		$actual = $this->service->initiate(array(
				'term' => $term, 
				'category' => Constants::SEARCH_CATEGORY_INSTITUTION
		));
		$this->assertNotEmpty($actual, "Searched for \"$term\" (search should be case-insensitive)");
		
		$term = 'nsti';
		$actual = $this->service->initiate(array(
				'term' => $term, 
				'category' => Constants::SEARCH_CATEGORY_INSTITUTION
		));
		$this->assertNotEmpty($actual, "Searched for \"$term\"");

		/*
		$term = 'Test    Institution Medical      Clinic';
		$actual = $this->service->initiate(array(
				'term' => $term,
				'category' => Constants::SEARCH_CATEGORY_INSTITUTION
		));
		$this->assertNotEmpty($actual, "Searched for \"$term\" (search should be whitespace-insensitive)");
		*/		
	}
	
	public function testInitiateSearchForInstitutionsShouldReturnAnEmptyArray()
	{
		$term ='A clinic that should not exist: '.$this->time;		
		$actual = $this->service->initiate(array(
				'term' => $term, 
				'category' => Constants::SEARCH_CATEGORY_INSTITUTION
		));
		$this->assertTrue(is_array($actual), 'Method initiate() should return an array');
		$this->assertEmpty($actual, "Searched for \"$term\"");
	}

	public function testInitiateShouldReturnArrayOfMedicalCenterObjects()
	{
		$term = 'AddedFromTest Center';
		$actual = $this->service->initiate(array(
				'term' => $term, 
				'category' => Constants::SEARCH_CATEGORY_CENTER
		));
		$this->assertTrue(is_array($actual), 'Method initiate() should return an array');
		$this->assertNotEmpty($actual, "Searched for \"$term\"");
		$this->assertInstanceOf(
				'HealthCareAbroad\\MedicalProcedureBundle\\Entity\\MedicalCenter', $actual[0], 
				'Method initiate() should return an array of MedicalCenter objects');
	
		$term = 'aDDedfRomTEst Center';		
		$actual = $this->service->initiate(array(
				'term' => $term, 
				'category' => Constants::SEARCH_CATEGORY_CENTER
		));
		$this->assertNotEmpty($actual, "Searched for \"$term\" (search should be case-insensitive)");
	
		$term = 'dedFromT';
		$actual = $this->service->initiate(array(
				'term' => $term, 
				'category' => Constants::SEARCH_CATEGORY_CENTER
		));
		$this->assertNotEmpty($actual, "Searched for \"$term\"");

		/*
		$term = 'AddedFromTest       Center';
		$actual = $this->service->initiate(array(
				'term' => $term,
				'category' => Constants::SEARCH_CATEGORY_CENTER
		));
		$this->assertNotEmpty($actual, "Searched for \"$term\" (search should be whitespace-insensitive)");
		*/		
	}
	
	public function testInitiateSearchForMedicalCentersShouldReturnAnEmptyArray()
	{
		$term = 'A center that should not exist: '.$this->time;
		$actual = $this->service->initiate(array(
				'term' => $term, 
				'category' => Constants::SEARCH_CATEGORY_CENTER
		));
		$this->assertTrue(is_array($actual), 'Method initiate() should return an array');
		$this->assertEmpty($actual, "Searched for \"$term\"");
	}
	
	public function testInitiateShouldReturnArrayOfProcedureTypeObjects()
	{
		$term = "testtype";
		$actual = $this->service->initiate(array(
				'term' => $term,
				'category' => Constants::SEARCH_CATEGORY_PROCEDURE_TYPE
		));
		$this->assertTrue(is_array($actual), 'Method initiate() should return an array');
		$this->assertNotEmpty($actual, "Searched for \"$term\"");
		$this->assertInstanceOf(
				'HealthCareAbroad\\MedicalProcedureBundle\\Entity\\MedicalProcedureType', $actual[0], 
				'Method initiate() should return an array of ProcedureType objects');
	
		$term = "TestTYPE";
		$actual = $this->service->initiate(array(
				'term' => $term,
				'category' => Constants::SEARCH_CATEGORY_PROCEDURE_TYPE
		));
		$this->assertNotEmpty($actual, "Searched for \"$term\" (search should be case-insensitive)");
	
		$term = "estty";
		$actual = $this->service->initiate(array(
				'term' => $term,
				'category' => Constants::SEARCH_CATEGORY_PROCEDURE_TYPE
		));
		$this->assertNotEmpty($actual, "Searched for \"$term\"");

		//TODO: setup fixtures
		/*
		$term = '';
		$actual = $this->service->initiate(array(
				'term' => $term,
				'category' => Constants::SEARCH_CATEGORY_PROCEDURE_TYPE
		));
		$this->assertNotEmpty($actual, "Searched for \"$term\" (search should be whitespace-insensitive)");
		*/
	}
	
	public function testInitiateSearchForProcedureTypesShouldReturnAnEmptyArray()
	{
		$term = 'A procedure type that should not exist: '.$this->time;
		$actual = $this->service->initiate(array(
				'term' => $term, 
				'category' => Constants::SEARCH_CATEGORY_PROCEDURE_TYPE
		));
		$this->assertTrue(is_array($actual), 'Method initiate() should return an array');
		$this->assertEmpty($actual, "Searched for \"$term\"");
	}
	
	public function testInitiateShouldReturnArrayOfProcedureObjects()
	{
		$term = '';
		$actual = $this->service->initiate(array(
				'term' => $term,
				'category' => Constants::SEARCH_CATEGORY_PROCEDURE
		));
		
		$this->assertTrue(is_array($actual), 'Method initiate() should return an array');
		$this->assertNotEmpty($actual, "Searched for \"$term\"");
		$this->assertInstanceOf(
				'HealthCareAbroad\\MedicalProcedureBundle\\Entity\\MedicalProcedure', $actual[0],
				'Method initiate() should return an array of MedicalProcedure objects');
		
		
		
		$term = "TEstProCEDure1";
		$actual = $this->service->initiate(array(
				'term' => $term,
				'category' => Constants::SEARCH_CATEGORY_PROCEDURE
		));
		$this->assertNotEmpty($actual, "Searched for \"$term\" (search should be case-insensitive)");
	
		$term = "tProce";
		$actual = $this->service->initiate(array(
				'term' => $term,
				'category' => Constants::SEARCH_CATEGORY_PROCEDURE
		));
		$this->assertNotEmpty($actual, "Searched for \"$term\"");
	
		//TODO: setup fixtures
		/*
			$term = '';
		$actual = $this->service->initiate(array(
				'term' => $term,
				'category' => Constants::SEARCH_CATEGORY_PROCEDURE
		));
		$this->assertNotEmpty($actual, "Searched for \"$term\" (search should be whitespace-insensitive)");
		*/
	}
	
	public function testInitiateSearchForProceduresShouldReturnAnEmptyArray()
	{
		$term = 'A procedure that should not exist: '.$this->time;
		$actual = $this->service->initiate(array(
				'term' => $term,
				'category' => Constants::SEARCH_CATEGORY_PROCEDURE
		));
		$this->assertTrue(is_array($actual), 'Method initiate() should return an array');
		$this->assertEmpty($actual, "Searched for \"$term\"");
	}

}