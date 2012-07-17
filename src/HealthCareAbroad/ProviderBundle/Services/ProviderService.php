<?php

namespace HealthCareAbroad\ProviderBundle\Services;

class ProviderService
{
	protected $doctrine;

	public function setDoctrine(\Doctrine\Bundle\DoctrineBundle\Registry $doctrine)
    {
    	$this->doctrine = $doctrine;
    }

    public function getProviders()
    {
    	return $this->doctrine->getEntityManager()->createQueryBuilder()
				->add('select', 'p')
				->add('from', 'ProviderBundle:Provider p')
				->add('where', 'p.status = 1');
    }
    
}