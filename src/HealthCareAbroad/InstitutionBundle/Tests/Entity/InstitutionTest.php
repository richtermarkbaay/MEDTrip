<?php

namespace HealthCareAbroad\InstitutionBundle\Tests;

use HealthCareAbroad\InstitutionBundle\Entity\Institution;
use HealthCareAbroad\InstitutionBundle\Entity\InstitutionStatus;
use HealthCareAbroad\InstitutionBundle\Tests\InstitutionBundleTestCase;

class InstitutionTest extends InstitutionBundleTestCase
{
    /**
     * @var HealthCareAbroad\InstitutionBundle\Entity\Institution
     */
    private $institution = null;
    
    public function setUp()
    {
        $this->institution = new Institution();    
    }
    
    public function testSetAsActive()
    {
        $this->institution->setAsActive();
        $this->assertEquals(InstitutionStatus::ACTIVE, $this->institution->getStatus());
    }
    
    public function testSetAsInactive()
    {
        $this->institution->setAsInactive();
        $this->assertEquals(InstitutionStatus::INACTIVE, $this->institution->getStatus());
    }
    
    public function testSetAsUnapproved()
    {
        $this->institution->setAsUnapproved();
        $this->assertEquals(InstitutionStatus::ACTIVE+InstitutionStatus::UNAPPROVED, $this->institution->getStatus());
    }
    
    public function testAsApproved()
    {
        $this->institution->setAsApproved();
        $this->assertEquals(InstitutionStatus::ACTIVE+InstitutionStatus::APPROVED, $this->institution->getStatus());
    }
    
    public function testAsSuspended()
    {
        $this->institution->setAsSuspended();
        $this->assertEquals(InstitutionStatus::ACTIVE+InstitutionStatus::SUSPENDED, $this->institution->getStatus());
    }
    
    public function testIsActive()
    {
        $this->institution->setAsActive();
        $this->assertTrue($this->institution->isActive());
        
        $this->institution->setAsApproved();
        $this->assertTrue($this->institution->isActive(), 'Isactive should be true after setting as Approved');
        
        $this->institution->setAsSuspended();
        $this->assertTrue($this->institution->isActive(), 'Isactive should be true after setting as Suspended');
        
        $this->institution->setAsUnapproved();
        $this->assertTrue($this->institution->isActive(), 'Isactive should be true after setting as Unapproved');
        
        $this->institution->setAsInactive();
        $this->assertFalse($this->institution->isActive(), 'Isactive should be false after setting as Inactive');
    }
    
    public function testIsInactive()
    {
        $this->institution->setAsInactive();
        $this->assertTrue($this->institution->isInactive(), 'Is inactive should be true after setting as Inactive');
        
        $this->institution->setAsActive();
        $this->assertFalse($this->institution->isInactive(), 'Is inactive should be false after setting as Active');
        
        $this->institution->setAsApproved();
        $this->assertFalse($this->institution->isInactive(), 'Is inactive should be false after setting as Approved');
        
        $this->institution->setAsSuspended();
        $this->assertFalse($this->institution->isInactive(), 'Is inactive should be false after setting as Suspended');
        
        $this->institution->setAsUnapproved();
        $this->assertFalse($this->institution->isInactive(), 'Is inactive should be false after setting as Unapproved');
    }
    
    public function testIsUnapproved()
    {
        $this->institution->setAsUnapproved();
        $this->assertTrue($this->institution->isUnapproved(), 'Is unapproved should be true after setting as Unapproved');
        
        $this->institution->setAsActive();
        $this->assertFalse($this->institution->isUnapproved(), 'Is unapproved should be false after setting as Active');
        
        $this->institution->setAsInactive();
        $this->assertFalse($this->institution->isUnapproved(), 'Is unapproved should be false after setting as Inactive');
        
        $this->institution->setAsApproved();
        $this->assertFalse($this->institution->isUnapproved(), 'Is unapproved should be false after setting as Approved');
        
        $this->institution->setAsSuspended();
        $this->assertFalse($this->institution->isUnapproved(), 'Is unapproved should be false after setting as Suspended');
    }
    
    public function testIsApproved()
    {
        $this->institution->setAsApproved();
        $this->assertTrue($this->institution->isApproved(), 'Is approved should be true after setting as Approved');
        
        $this->institution->setAsUnapproved();
        $this->assertFalse($this->institution->isApproved(), 'Is approved should be false after setting as Unapproved');
        
        $this->institution->setAsActive();
        $this->assertFalse($this->institution->isApproved(), 'Is approved should be false after setting as Active');
        
        $this->institution->setAsInactive();
        $this->assertFalse($this->institution->isApproved(), 'Is approved should be false after setting as Inactive');
        
        $this->institution->setAsSuspended();
        $this->assertFalse($this->institution->isApproved(), 'Is approved should be false after setting as Suspended');
    }
    
    public function testIsSuspended()
    {
        $this->institution->setAsSuspended();
        $this->assertTrue($this->institution->isSuspended(), 'Is suspended should be true after setting as Suspended');
        
        $this->institution->setAsApproved();
        $this->assertFalse($this->institution->isSuspended(), 'Is suspended should be false after setting as Approved');
        
        $this->institution->setAsUnapproved();
        $this->assertFalse($this->institution->isSuspended(), 'Is suspended should be false after setting as Unapproved');
        
        $this->institution->setAsActive();
        $this->assertFalse($this->institution->isSuspended(), 'Is suspended should be false after setting as Active');
        
        $this->institution->setAsInactive();
        $this->assertFalse($this->institution->isSuspended(), 'Is suspended should be false after setting as Inactive');
    }
}