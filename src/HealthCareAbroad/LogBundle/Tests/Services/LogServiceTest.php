<?php
namespace HealthCareAbroad\LogBundle\Tests\Services;

use HealthCareAbroad\LogBundle\Services\LogService;

use HealthCareAbroad\LogBundle\Entity\Log;

use HealthCareAbroad\LogBundle\Tests\LogBundleUnitTestCase;

class LogServiceTest extends LogBundleUnitTestCase
{
    /**
     * @var LogService
     */
    private $service;
    
    public function setUp()
    {
        $this->service = new LogService($this->getServiceContainer());
    }
    
    
    public function testGetLogClassByName()
    {
        $className = 'HealthCareAbroad\LogBundle\Entity\Log';
        // test get class of not recorded class
        $countBefore = \count($this->getDoctrine()->getRepository('LogBundle:LogClass')->findAll());
        
        $logClass = $this->service->getLogClassByName($className);
        $this->assertEquals($logClass->getName(), $className);
        
        $countAfter = \count($this->doctrine->getRepository('LogBundle:LogClass')->findAll());
        $this->assertEquals($countBefore+1, $countAfter, 'Expecting added object count after saving to database');
        
        // test get class for already recorded log class
        $this->service->getLogClassByName($className);
        $this->assertEquals($countAfter, \count($this->doctrine->getRepository('LogBundle:LogClass')->findAll()), 'Expecting same object count after getting log class of recorded class');
    }
    
    /**
     * @expectedException HealthCareAbroad\LogBundle\Exception\ListenerException
     */
    public function testGetLogClassByNameOfNoneExistingClass()
    {
        $logClass = $this->service->getLogClassByName('ThisClassShouldNeverEverExist13456');
    }
}