<?php

namespace HealthCareAbroad\FrontendBundle\Services;

use HealthCareAbroad\AdminBundle\Entity\InquirySubject;
use HealthCareAbroad\AdminBundle\Entity\Inquiry;

class InquireService
{
	protected $doctrine;

	public function __construct(\Doctrine\Bundle\DoctrineBundle\Registry $doctrine )
    {
    	$this->doctrine = $doctrine;
    }

	public function getActiveInquirySubjects()
	{
		return $this->doctrine->getEntityManager()->createQueryBuilder()
		->add('select', 'a')
		->add('from', 'AdminBundle:InquirySubject a')
		->add('where', 'a.status = 1');
	}
	
	public function createInquiry(Inquiry $inquiry)
	{
		$em = $this->doctrine->getEntityManager();
		$em->persist($inquiry);
		$em->flush();
		
		// failed to save
		if (!$inquiry) {
			return $this->_errorResponse(500, 'Exception encountered upon persisting data.');
		}
		
		return $inquiry;
	}
}