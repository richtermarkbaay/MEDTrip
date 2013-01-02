<?php
namespace HealthCareAbroad\SearchBundle\Tests\Services;

use HealthCareAbroad\SearchBundle\Tests\ContainerAwareUnitTestCase;
use HealthCareAbroad\SearchBundle\Services\SearchParameterBag;

class SearchServiceTest extends ContainerAwareUnitTestCase
{
    public function setUp()
    {
        $this->service = $this->get('services.search');
    }

    public function tearDown()
    {
    }

    public function testGetDestinationsByCountry()
    {
        $searchParams = new SearchParameterBag(array(
                        'term' => 'Phil',
                        'destination' => '0-0',
                        'treatment' => '0-0-0-0',
                        'destinationLabel' => 'Phil',
                        'treatmentLabel' => ''
        ));

        $results = $this->service->getDestinations($searchParams);

        $this->assertArrayHasKey('label', $results[0]);
        $this->assertEquals('Philippines', $results[0]['label']);
        $this->assertArrayHasKey('value', $results[0]);
        $this->assertEquals('1-0', $results[0]['value']);
    }

    public function testGetDestinationsByCity()
    {
        $searchParams = new SearchParameterBag(array(
                        'term' => 'eb',
                        'destination' => '0-0',
                        'treatment' => '0-0-0-0',
                        'destinationLabel' => 'eb',
                        'treatmentLabel' => ''
        ));

        $results = $this->service->getDestinations($searchParams);
        $this->assertArrayHasKey('label', $results[0]);
        $this->assertEquals('cebu, Philippines', $results[0]['label']);
        $this->assertArrayHasKey('value', $results[0]);
        $this->assertEquals('1-1', $results[0]['value']);
    }

    public function testGetTreatmentsBySpecialization()
    {
        $searchParams = new SearchParameterBag(array(
                        'term' => 'specialization',
                        'destination' => '0-0',
                        'treatment' => '0-0-0-0',
                        'destinationLabel' => '',
                        'treatmentLabel' => 'specialization'
        ));

        $results = $this->service->getTreatments($searchParams);

        $this->assertCount(2, $results);
        foreach ($results as $r) {
            $this->assertArrayHasKey('label', $r);
            $this->assertArrayHasKey('value', $r);

            if ($r['label'] === 'Specialization 1') {
                $this->assertEquals('1-0-0-specialization', $r['value']);
            } else {
                $this->assertEquals('Treatment with sub specialization', $r['label']);
                $this->assertEquals('1-1-1-treatment', $r['value']);
            }
        }
    }

    public function testGetTreatmentsBySubSpecialization()
    {
        $searchParams = new SearchParameterBag(array(
                        'term' => 'ub with treatment',
                        'destination' => '0-0',
                        'treatment' => '0-0-0-0',
                        'destinationLabel' => '',
                        'treatmentLabel' => 'ub with treatment'
        ));

        $results = $this->service->getTreatments($searchParams);
        $this->assertCount(1, $results);
        $this->assertArrayHasKey('label', $results[0]);
        $this->assertEquals('Sub with treatments', $results[0]['label']);
        $this->assertArrayHasKey('value', $results[0]);
        $this->assertEquals('1-1-0-subSpecialization', $results[0]['value']);
    }

    public function testGetTreatmentsByTreatment()
    {
        $searchParams = new SearchParameterBag(array(
                        'term' => 'treatment with sub',
                        'destination' => '0-0',
                        'treatment' => '0-0-0-0',
                        'destinationLabel' => '',
                        'treatmentLabel' => 'treatment with sub'
        ));

        $results = $this->service->getTreatments($searchParams);
        $this->assertCount(1, $results);
        $this->assertArrayHasKey('label', $results[0]);
        $this->assertEquals('Treatment with sub specialization', $results[0]['label']);
        $this->assertArrayHasKey('value', $results[0]);
        $this->assertEquals('1-1-1-treatment', $results[0]['value']);
    }

    public function testGetDestinationByCountryAndSpecialization()
    {
        $searchParams = new SearchParameterBag(array(
                        'term' => 'philippine',
                        'destination' => '0-0',
                        'treatment' => '1-0-0-specialization',
                        'destinationLabel' => 'philippine',
                        'treatmentLabel' => 'Specialization 1'
        ));

        $results = $this->service->getDestinations($searchParams);

        $this->assertCount(2, $results);
        foreach ($results as $r) {
            $this->assertArrayHasKey('label', $r);
            $this->assertArrayHasKey('value', $r);

            if ($r['label'] === 'Philippines') {
                $this->assertEquals('1-0', $r['value']);
            } else {
                $this->assertEquals('cebu, Philippines', $r['label']);
                $this->assertEquals('1-1', $r['value']);
            }
        }
    }

    public function testGetDestinationsByCityAndSpecialization()
    {
        $searchParams = new SearchParameterBag(array(
                        'term' => 'Ceb',
                        'destination' => '0-0',
                        'treatment' => '1-0-0-specialization',
                        'destinationLabel' => 'Ceb',
                        'treatmentLabel' => 'Specialization 1'
        ));

        $results = $this->service->getDestinations($searchParams);

        $this->assertCount(1, $results);

        $this->assertArrayHasKey('label', $results[0]);
        $this->assertEquals('cebu, Philippines', $results[0]['label']);
        $this->assertArrayHasKey('value', $results[0]);
        $this->assertEquals('1-1', $results[0]['value']);
    }

    public function testGetDestinationsByCountryAndSubSpecialization()
    {
        $searchParams = new SearchParameterBag(array(
                        'term' => 'philippine',
                        'destination' => '0-0',
                        'treatment' => '1-1-0-subSpecialization',
                        'destinationLabel' => 'philippine',
                        'treatmentLabel' => 'Sub with treatments'
        ));

        $results = $this->service->getDestinations($searchParams);

        $this->assertCount(2, $results);
        foreach ($results as $r) {
            $this->assertArrayHasKey('label', $r);
            $this->assertArrayHasKey('value', $r);

            if ($r['label'] === 'Philippines') {
                $this->assertEquals('1-0', $r['value']);
            } else {
                $this->assertEquals('cebu, Philippines', $r['label']);
                $this->assertEquals('1-1', $r['value']);
            }
        }
    }

    public function testGetDestinationsByCityAndSubSpecialization()
    {
        $searchParams = new SearchParameterBag(array(
                        'term' => 'Ceb',
                        'destination' => '0-0',
                        'treatment' => '1-1-0-subSpecialization',
                        'destinationLabel' => 'Ceb',
                        'treatmentLabel' => 'Sub with treatments'
        ));

        $results = $this->service->getDestinations($searchParams);

        $this->assertCount(1, $results);
        $this->assertArrayHasKey('label', $results[0]);
        $this->assertEquals('cebu, Philippines', $results[0]['label']);
        $this->assertArrayHasKey('value', $results[0]);
        $this->assertEquals('1-1', $results[0]['value']);
    }

    public function testGetDestinationsByCountryAndTreatment()
    {
        $searchParams = new SearchParameterBag(array(
                        'term' => 'Philippine',
                        'destination' => '0-0',
                        'treatment' => '1-1-1-treatment',
                        'destinationLabel' => 'Philippine',
                        'treatmentLabel' => 'Treatment with sub specialization'
        ));

        $results = $this->service->getTreatments($searchParams);

        $this->assertCount(2, $results);
        foreach ($results as $r) {
            $this->assertArrayHasKey('label', $r);
            $this->assertArrayHasKey('value', $r);

            if ($r['label'] === 'Philippines') {
                $this->assertEquals('1-0', $r['value']);
            } else {
                $this->assertEquals('cebu, Philippines', $r['label']);
                $this->assertEquals('1-1', $r['value']);
            }
        }
    }

    public function testGetDestinationsByCityAndTreatment()
    {
        $searchParams = new SearchParameterBag(array(
                        'term' => 'Ceb',
                        'destination' => '0-0',
                        'treatment' => '1-1-1-treatment',
                        'destinationLabel' => 'Ceb',
                        'treatmentLabel' => 'Treatment with sub specialization'
        ));

        $results = $this->service->getTreatments($searchParams);

        $this->assertCount(1, $results);
        $this->assertArrayHasKey('label', $results[0]);
        $this->assertArrayHasKey('value', $results[0]);
        $this->assertEquals('cebu, Philippines', $results[0]['label']);
        $this->assertEquals('1-1', $results[0]['value']);
    }

    public function testGetTreatmentsBySpecializationAndCountry()
    {
        $searchParams = new SearchParameterBag(array(
                        'term' => 'specialization',
                        'destination' => '1-0',
                        'treatment' => '0-0-0-0',
                        'destinationLabel' => 'Philippines',
                        'treatmentLabel' => 'specialization'
        ));

        $results = $this->service->getTreatments($searchParams);
        $this->assertCount(2, $results);

        foreach ($results as $r) {
            $this->assertArrayHasKey('label', $r);
            $this->assertArrayHasKey('value', $r);

            if ($r['label'] === 'Specialization 1') {
                $this->assertEquals('1-0-0-specialization', $r['value']);
            } else {
                $this->assertEquals('Treatment with sub specialization', $r['label']);
                $this->assertEquals('1-1-1-treatment', $r['value']);
            }
        }
    }

    public function testGetTreatmentsBySpecializationAndCity()
    {
        $searchParams = new SearchParameterBag(array(
                        'term' => 'specialization',
                        'destination' => '1-1',
                        'treatment' => '0-0-0-0',
                        'destinationLabel' => 'Cebu, Philippines',
                        'treatmentLabel' => 'specialization'
        ));

        $results = $this->service->getTreatments($searchParams);
        $this->assertCount(2, $results);

        foreach ($results as $r) {
            $this->assertArrayHasKey('label', $r);
            $this->assertArrayHasKey('value', $r);

            if ($r['label'] === 'Specialization 1') {
                $this->assertEquals('1-0-0-specialization', $r['value']);
            } else {
                $this->assertEquals('Treatment with sub specialization', $r['label']);
                $this->assertEquals('1-1-1-treatment', $r['value']);
            }
        }
    }

    public function testGetTreatmentsBySubSpecializationAndCountry()
    {
        $searchParams = new SearchParameterBag(array(
                        'term' => 'ub with treatment',
                        'destination' => '1-0',
                        'treatment' => '0-0-0-0',
                        'destinationLabel' => 'Philippines',
                        'treatmentLabel' => 'ub with treatment'
        ));

        $results = $this->service->getTreatments($searchParams);
        $this->assertCount(1, $results);
        $this->assertArrayHasKey('label', $results[0]);
        $this->assertEquals('Sub with treatments', $results[0]['label']);
        $this->assertArrayHasKey('value', $results[0]);
        $this->assertEquals('1-1-0-subSpecialization', $results[0]['value']);
    }

    public function testGetTreatmentsBySubSpecializationAndCity()
    {
        $searchParams = new SearchParameterBag(array(
                        'term' => 'ub with treatment',
                        'destination' => '1-1',
                        'treatment' => '0-0-0-0',
                        'destinationLabel' => 'Cebu, Philippines',
                        'treatmentLabel' => 'ub with treatment'
        ));

        $results = $this->service->getTreatments($searchParams);
        $this->assertCount(1, $results);
        $this->assertArrayHasKey('label', $results[0]);
        $this->assertEquals('Sub with treatments', $results[0]['label']);
        $this->assertArrayHasKey('value', $results[0]);
        $this->assertEquals('1-1-0-subSpecialization', $results[0]['value']);
    }

    public function testGetTreatmentsByTreatmentAndCountry()
    {
        $searchParams = new SearchParameterBag(array(
                        'term' => 'treatment with sub',
                        'destination' => '1-0',
                        'treatment' => '0-0-0-0',
                        'destinationLabel' => 'Philippines',
                        'treatmentLabel' => 'treatment with sub'
        ));

        $results = $this->service->getTreatments($searchParams);
        $this->assertCount(1, $results);
        $this->assertArrayHasKey('label', $results[0]);
        $this->assertEquals('Treatment with sub specialization', $results[0]['label']);
        $this->assertArrayHasKey('value', $results[0]);
        $this->assertEquals('1-1-1-treatment', $results[0]['value']);
    }

    public function testGetTreatmentsByTreatmentAndCity()
    {
        $searchParams = new SearchParameterBag(array(
                        'term' => 'treatment with sub',
                        'destination' => '1-1',
                        'treatment' => '0-0-0-0',
                        'destinationLabel' => 'Cebu, Philippines',
                        'treatmentLabel' => 'treatment with sub'
        ));

        $results = $this->service->getTreatments($searchParams);
        $this->assertCount(1, $results);
        $this->assertArrayHasKey('label', $results[0]);
        $this->assertEquals('Treatment with sub specialization', $results[0]['label']);
        $this->assertArrayHasKey('value', $results[0]);
        $this->assertEquals('1-1-1-treatment', $results[0]['value']);
    }
}