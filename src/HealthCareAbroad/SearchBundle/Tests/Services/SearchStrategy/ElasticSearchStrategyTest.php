<?php
namespace HealthCareAbroad\SearchBundle\Tests\Services\SearchStrategy;

use HealthCareAbroad\SearchBundle\Services\SearchParameterBag;
use HealthCareAbroad\SearchBundle\Tests\ContainerAwareUnitTestCase;
use HealthCareAbroad\InstitutionBundle\Entity\Institution;

class ElasticSearchStrategyTest extends ContainerAwareUnitTestCase
{
    public function setUp()
    {
    }

    public function tearDown()
    {
    }

    public function testSearchAutocompleteCountry()
    {
        $searchParams = new SearchParameterBag(array(
                        'term' => 'Philippines',
                        'destination' => '1-0',
                        'treatment' => '0-0-0-0',
                        'destinationLabel' => '',
                        'treatmentLabel' => 'Philippines'
        ));

        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testSearchAutocompleteCity()
    {
        $searchParams = new SearchParameterBag(array(
                        'term' => 'Cebu',
                        'destination' => '1-1',
                        'treatment' => '0-0-0-0',
                        'destinationLabel' => 'Cebu',
                        'treatmentLabel' => ''
        ));

        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testSearchAutocompleteSpecialization()
    {
        $searchParams = new SearchParameterBag(array(
                        'term' => 'specialization',
                        'destination' => '0-0',
                        'treatment' => '1-0-0-specialization',
                        'destinationLabel' => '',
                        'treatmentLabel' => 'specialization'
        ));

        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testSearchAutocompleteSubSpecialization()
    {
        $searchParams = new SearchParameterBag(array(
                        'term' => 'sub with treatments',
                        'destination' => '0-0',
                        'treatment' => '1-1-0-subSpecialization',
                        'destinationLabel' => '',
                        'treatmentLabel' => 'sub with treatments'
        ));

        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testSearchAutocompleteTreatment()
    {
        $searchParams = new SearchParameterBag(array(
                        'term' => 'treatment with sub specialization',
                        'destination' => '0-0',
                        'treatment' => '1-0-1-treatment',
                        'destinationLabel' => '',
                        'treatmentLabel' => 'treatment with sub specialization'
        ));

        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testSearchAutocompleteCountrySpecialization()
    {
        $searchParams = new SearchParameterBag(array(
                        'term' => '',
                        'destination' => '1-0',
                        'treatment' => '1-0-1-specialization',
                        'destinationLabel' => '',
                        'treatmentLabel' => ''
        ));

        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testSearchAutocompleteCitySpecialization()
    {
        $searchParams = new SearchParameterBag(array(
                        'term' => '',
                        'destination' => '1-1',
                        'treatment' => '1-0-1-specialization',
                        'destinationLabel' => '',
                        'treatmentLabel' => ''
        ));

        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testSearchAutocompleteCountrySubSpecialization()
    {
        $searchParams = new SearchParameterBag(array(
                        'term' => '',
                        'destination' => '1-1',
                        'treatment' => '1-0-1-specialization',
                        'destinationLabel' => '',
                        'treatmentLabel' => ''
        ));

        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testSearchAutocompleteCitySubSpecialization()
    {
        $searchParams = new SearchParameterBag(array(
                        'term' => '',
                        'destination' => '1-1',
                        'treatment' => '1-0-1-specialization',
                        'destinationLabel' => '',
                        'treatmentLabel' => ''
        ));

        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testSearchAutocompleteCountryTreatment()
    {
        $searchParams = new SearchParameterBag(array(
                        'term' => '',
                        'destination' => '1-1',
                        'treatment' => '1-0-1-specialization',
                        'destinationLabel' => '',
                        'treatmentLabel' => ''
        ));

        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testSearchAutocompleteCityTreatment()
    {
        $searchParams = new SearchParameterBag(array(
                        'term' => '',
                        'destination' => '1-1',
                        'treatment' => '1-0-1-specialization',
                        'destinationLabel' => '',
                        'treatmentLabel' => ''
        ));

        $this->markTestIncomplete('This test has not been implemented yet.');
    }

}