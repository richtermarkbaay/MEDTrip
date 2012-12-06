<?php
namespace HealthCareAbroad\InstitutionBundle\Tests\Services;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionStatus;

use HealthCareAbroad\InstitutionBundle\Entity\Institution;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionTypes;

use HealthCareAbroad\InstitutionBundle\Services\InstitutionFactory;

use HealthCareAbroad\InstitutionBundle\Tests\InstitutionBundleTestCase;

/**
 * 
 * @author Allejo Chris G. Velarde
 *
 */
class InstitutionFactoryTest extends InstitutionBundleTestCase
{
    /**
     * @var InstitutionFactory
     */
    private $factory;
    
    public function setup()
    {
        $this->factory = $this->getServiceContainer()->get('services.institution.factory');   
    }
    
    private function _setCommonInstitutionData(Institution &$institution)
    {
        $city = $this->getDoctrine()->getRepository('HelperBundle:City')->find(1);
        $institution->setAddress1('adfasdf');
        $institution->setAddress2('address 2');
        $institution->setCity($city);
        $institution->setContactEmail('test-only@chromedia.com');
        $institution->setContactNumber('1111');
        $institution->setCountry($city->getCountry());
        $institution->setDescription('');
        $institution->setLogo('logo.jpg');
        $institution->setStatus(InstitutionStatus::ACTIVE);
        $institution->setZipCode(6500);
    }
    
    public function testCreateByType()
    {
        // test create medical group network member
        $institution = $this->factory->createByType(InstitutionTypes::MULTIPLE_CENTER);
        $this->assertInstanceOf('HealthCareAbroad\InstitutionBundle\Entity\MedicalGroupNetworkMember', $institution);
        
        // test medical tourism facilitator
        $institution = $this->factory->createByType(InstitutionTypes::MEDICAL_TOURISM_FACILITATOR);
        $this->assertInstanceOf('HealthCareAbroad\InstitutionBundle\Entity\MedicalTourismFacilitator', $institution);
        
        // test independent hospital
        $institution = $this->factory->createByType(InstitutionTypes::SINGLE_CENTER);
        $this->assertInstanceOf('HealthCareAbroad\InstitutionBundle\Entity\IndependentHospital', $institution);
    }
    
    /**
     * @expectedException HealthCareAbroad\InstitutionBundle\Exception\InstitutionFactoryException
     */
    public function testCreateByInvalidType()
    {
        $this->factory->createByType(999999);
    }
    
    public function testSave()
    {
        $institution = $this->factory->createByType(InstitutionTypes::SINGLE_CENTER);
        $name = 'test institution '.time();
        $institution->setName($name);
        $this->_setCommonInstitutionData($institution);
        
        $this->factory->save($institution);
        $this->assertGreaterThan(0, $institution->getId());
        
        $inst2 = $this->getDoctrine()->getRepository('InstitutionBundle:Institution')->find($institution->getId());
        $this->assertEquals($institution->getId(), $inst2->getId());
        
        return $institution;
    }
    
    /**
     * @depends testSave
     */
    public function testFindBySlug(Institution $institution)
    {
        $inst2 = $this->factory->findBySlug($institution->getSlug());
        $this->assertEquals($institution->getId(), $inst2->getId());
    }
    
    /**
     * @depends testSave
     */
    public function testFindById(Institution $institution)
    {
        $inst2 = $this->factory->findById($institution->getId());
        $this->assertEquals($institution->getId(), $inst2->getId());
    }
}