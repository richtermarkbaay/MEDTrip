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

    public function testInitiateShouldReturnArrayOfTreatmentObjects()
    {
        $term = "Procedure Type1";
        $actual = $this->service->initiate(array(
                'term' => $term,
                'category' => Constants::SEARCH_CATEGORY_PROCEDURE_TYPE
        ));
        $this->assertTrue(is_array($actual), 'Method initiate() should return an array');
        $this->assertNotEmpty($actual, "Searched for \"$term\"");
        $this->assertInstanceOf(
                'HealthCareAbroad\\MedicalProcedureBundle\\Entity\\Treatment', $actual[0],
                'Method initiate() should return an array of ProcedureType objects');

        $term = "ProCedure Type1";
        $actual = $this->service->initiate(array(
                'term' => $term,
                'category' => Constants::SEARCH_CATEGORY_PROCEDURE_TYPE
        ));
        $this->assertNotEmpty($actual, "Searched for \"$term\" (search should be case-insensitive)");

        $term = "dure Ty";
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

    public function testInitiateSearchForTreatmentsShouldReturnAnEmptyArray()
    {
        $term = 'A procedure type that should not exist: '.$this->time;
        $actual = $this->service->initiate(array(
                'term' => $term,
                'category' => Constants::SEARCH_CATEGORY_PROCEDURE_TYPE
        ));
        $this->assertTrue(is_array($actual), 'Method initiate() should return an array');
        $this->assertEmpty($actual, "Searched for \"$term\"");
    }

    public function testInitiateShouldReturnArrayOfTreatmentProcedureObjects()
    {
        $term = 'Test Medical Procedure';
        $actual = $this->service->initiate(array(
                'term' => $term,
                'category' => Constants::SEARCH_CATEGORY_PROCEDURE
        ));

        $this->assertTrue(is_array($actual), 'Method initiate() should return an array');
        $this->assertNotEmpty($actual, "Searched for \"$term\"");
        $this->assertInstanceOf(
                'HealthCareAbroad\\MedicalProcedureBundle\\Entity\\TreatmentProcedure', $actual[0],
                'Method initiate() should return an array of MedicalProcedure objects');



        $term = "Test medicaL PrOcedure";
        $actual = $this->service->initiate(array(
                'term' => $term,
                'category' => Constants::SEARCH_CATEGORY_PROCEDURE
        ));
        $this->assertNotEmpty($actual, "Searched for \"$term\" (search should be case-insensitive)");

        $term = "cal Pr";
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

    public function testInitiateSearchForTreatmentProceduresShouldReturnAnEmptyArray()
    {
        $term = 'A procedure that should not exist: '.$this->time;
        $actual = $this->service->initiate(array(
                'term' => $term,
                'category' => Constants::SEARCH_CATEGORY_PROCEDURE
        ));
        $this->assertTrue(is_array($actual), 'Method initiate() should return an array');
        $this->assertEmpty($actual, "Searched for \"$term\"");
    }


    public function testGetTreatmentsByName()
    {
        $name = "Type1";

        $actual = $this->service->getTreatmentsByName($name);
        $this->assertTrue(is_array($actual));
        $this->assertCount(1, $actual);
        $this->assertArrayHasKey('label', $actual[0]);
        $this->assertEquals('Procedure Type1', $actual[0]['label']);
        $this->assertArrayHasKey('value', $actual[0]);
        $this->assertEquals('1-0', $actual[0]['value']);

        $actual = $this->service->getTreatmentsByName($name, null);
        $this->assertTrue(is_array($actual));
        $this->assertCount(1, $actual);
        $this->assertArrayHasKey('label', $actual[0]);
        $this->assertEquals('Procedure Type1', $actual[0]['label']);
        $this->assertArrayHasKey('value', $actual[0]);
        $this->assertEquals('1-0', $actual[0]['value']);

        $actual = $this->service->getTreatmentsByName($name, 0);
        $this->assertTrue(is_array($actual));
        $this->assertCount(1, $actual);
        $this->assertArrayHasKey('label', $actual[0]);
        $this->assertEquals('Procedure Type1', $actual[0]['label']);
        $this->assertArrayHasKey('value', $actual[0]);
        $this->assertEquals('1-0', $actual[0]['value']);

        $name = 'non-existent-treatment';

        $actual = $this->service->getTreatmentsByName($name);
        $this->assertTrue(is_array($actual));
        $this->assertEmpty($actual);

        $actual = $this->service->getTreatmentsByName($name, null);
        $this->assertTrue(is_array($actual));
        $this->assertEmpty($actual);

        $actual = $this->service->getTreatmentsByName($name, 0);
        $this->assertTrue(is_array($actual));
        $this->assertEmpty($actual);
    }

    public function testGetDestinationsByName()
    {

    }

}