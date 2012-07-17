<?php

namespace HealthCareAbroad\ProviderBundle\Services;

class ProviderService
{

	public function setDoctrine(\Doctrine\Bundle\DoctrineBundle\Registry $doctrine)
    {
    	$this->doctrine = $doctrine;
    }
    
    
    protected $doctrine;
    
    
    public function getProviders()
    {
    	$providers = $this->doctrine->getEntityManager()->createQueryBuilder()
    	->add('select', 'p')
    	->add('from', 'ProviderBundle:Provider p')
    	->add('where', 'p.status = 1');
    
    	return $providers;
    }
    
}