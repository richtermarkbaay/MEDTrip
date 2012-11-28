<?php
/**
 * 
 * @author Allejo Chris G. Velarde
 */
namespace HealthCareAbroad\InstitutionBundle\Tests\Services;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionProperty;

use HealthCareAbroad\InstitutionBundle\Services\InstitutionPropertyService;

use HealthCareAbroad\InstitutionBundle\Tests\InstitutionBundleTestCase;

class InstitutionPropertyServiceTest extends InstitutionBundleTestCase
{
    /**
     * @var InstitutionPropertyService
     */
    private $service;
    
    public function setUp()
    {
        $this->service = $this->getServiceContainer()->get('services.institution_property');    
    }
    
    public function testCreateInstitutionPropertyByName()
    {
        $institution = $this->getServiceContainer()->get('services.institution.factory')->findById(1);
        $property = $this->service->createInstitutionPropertyByName('ancilliary_service_id', $institution);
        
        $this->assertInstanceOf('\HealthCareAbroad\InstitutionBundle\Entity\InstitutionProperty', $property);
        $this->assertEquals('ancilliary_service_id', $property->getInstitutionPropertyType()->getName());
        $this->assertEquals($institution->getId(), $property->getInstitution()->getId());
        
        return $property;
    }
    
    /**
     * @depends testCreateInstitutionPropertyByName
     */
    public function testSaveInstitutionProperty(InstitutionProperty $property)
    {
        $property->setValue("test value");
        $this->service->save($property);
        
        $this->assertGreaterThan(0, $property->getId());
    }
    
    /**
     * @expectedException HealthCareAbroad\InstitutionBundle\Exception\InstitutionPropertyException
     */
    public function testCreateInstitutionPropertyByNonExistentName()
    {
        $property = $this->service->createInstitutionPropertyByName('some_unknown_property_type');
    }
}