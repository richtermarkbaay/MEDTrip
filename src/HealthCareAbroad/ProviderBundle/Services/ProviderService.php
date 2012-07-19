<?php

namespace HealthCareAbroad\ProviderBundle\Services;

use HealthCareAbroad\ProviderBundle\Entity\ProviderUserInvitation;

use HealthCareAbroad\ProviderBundle\Entity\Provider;

class ProviderService
{
    protected $doctrine;

	public function setDoctrine(\Doctrine\Bundle\DoctrineBundle\Registry $doctrine)
    {
    	$this->doctrine = $doctrine;
    }
    
    /**
     * Get the current provider set in the session
     * 
     */
    public function getCurrentProvider()
    {
        $providerId = 1;
        return $this->doctrine->getRepository('ProviderBundle:Provider')->find($providerId);
    }

    public function getProviders()
    {
return $this->doctrine->getEntityManager()->createQueryBuilder()->add('select', 'p')->add('from', 'ProviderBundle:Provider p')->add('where', 'p.status=1');
// 		var_dump(get_class($x));

//     	exit;
//     	return $this->doctrine->getEntityManager()->createQueryBuilder()
// 				->add('select', 'p')
// 				->add('from', 'ProviderBundle:Provider p')
// 				->add('where', 'p.status = 1');

    }
}