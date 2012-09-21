<?php
/**
 * Unit test for InquireService
 * 
 * @author Alnie Jacobe
 *
 */
namespace HealthCareAbroad\FrontendBundle\Tests\Services;

use HealthCareAbroad\FrontendBundle\Services\InquireService;

use HealthCareAbroad\AdminBundle\Entity\Inquiry;
use HealthCareAbroad\AdminBundle\Entity\InquirySubject;
use HealthCareAbroad\FrontendBundle\Tests\FrontendBundleTestCase;

use HealthCareAbroad\UserBundle\Entity\SiteUser;

class InquireServiceTest extends FrontendBundleTestCase
{
	/**
	 *
	 * @var HealthCareAbroad\FrontendBundle\Services\InquireService
	 */
	protected $service;
	protected $doctrine;
	
	public function setUp()
	{
		$this->service = new InquireService($this->getDoctrine());
		$this->doctrine = $this->getDoctrine();
	}
	
	public function tearDown()
	{
		$this->service = null;
		$this->doctrine = null;
	}
	
	public function testCreateInquiry()
	{
		//get data for inquiry subject
		$inquirySubject = $this->doctrine->getRepository('AdminBundle:InquirySubject')->find(1);
		
		
		$inquire = new Inquiry();
		$inquire->setFirstName('test name');
		$inquire->setLastName('test lastname');
		$inquire->setEmail('test@test.com');
		$inquire->setInquirySubject($inquirySubject);
		$inquire->setMessage('this is test');
		$inquire->setStatus(SiteUser::STATUS_ACTIVE);
		$result = $this->service->createInquiry($inquire);
		
		$this->assertNotEmpty($result);
	}
	
}