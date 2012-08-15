<?php

namespace HealthCareAbroad\PageBundle\Services;

use HealthCareAbroad\AdminBundle\Entity\InquireAbout;
use HealthCareAbroad\AdminBundle\Entity\Inquire;

class InquireService
{
	protected $doctrine;

	public function setDoctrine(\Doctrine\Bundle\DoctrineBundle\Registry $doctrine)
	{
		$this->doctrine = $doctrine;
	}

	public function getActiveInquireAbouts()
	{
		return $this->doctrine->getEntityManager()->createQueryBuilder()
		->add('select', 'a')
		->add('from', 'AdminBundle:InquireAbout a')
		->add('where', 'a.status = 1');
	}
	
	public function createInquire(Inquire $inquire)
	{
		$em = $this->doctrine->getEntityManager();
		$em->persist($inquire);
		$em->flush();
		
		// failed to save
		if (!$inquire) {
			return $this->_errorResponse(500, 'Exception encountered upon persisting data.');
		}
		
		return $inquire;
	}
}