<?php

namespace HealthCareAbroad\HelperBundle\Repository;

use Doctrine\ORM\EntityRepository;

class PageMetaConfigurationRepository extends EntityRepository
{
    public function findOneByUrl($url)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        
        $qb->select('p')
        ->from('HelperBundle:PageMetaConfiguration', 'p')
        ->where('p.url = :url')
        ->setParameter('url', $url);
        
        return $qb->getQuery()->getOneOrNullResult();
    }
}