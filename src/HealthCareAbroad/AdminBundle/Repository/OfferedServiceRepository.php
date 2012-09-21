<?php

namespace HealthCareAbroad\AdminBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * OfferedServiceRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class OfferedServiceRepository extends EntityRepository
{
	function getOfferedServiceList() {
		$countries = $this->_em->getRepository('AdminBundle:OfferedService')->findByStatus(1);
		$arrOfferedService = array();
		foreach($OfferedService as $each){
			$arrOfferedService[$each->getId()] = $each->getName();
		}
	
		return $arrNews;
	}
	
	public function getLatestOfferedService($limit = null)
	{
		$qb = $this->createQueryBuilder('b')
		->select('b')
		->add('from', 'AdminBundle:OfferedService b')
		->addOrderBy('b.dateCreated', 'DESC');
	
		if (false === is_null($limit))
			$qb->setMaxResults($limit);
	
		return $qb->getQuery()
		->getResult();
	}

	public function getQueryBuilderForOfferedService()
	{
		return $this->_em->createQueryBuilder('c')
		->add('select', 'c')
		->add('from', 'AdminBundle:OfferedService c')
		->add('where', 'c.status = 1');
	}
	
	/**
	 * Get Active Service Offered
	 *
	 * @return Doctrine\ORM\QueryBuilder
	 */
	public function getBuilderForOfferedServices()
	{
	        $qb = $this->getEntityManager()->createQueryBuilder();
      		  $qb->select('a')
            ->from('AdminBundle:OfferedService', 'a')
            ->add('where', 'b.status = 1');

        return $qb;
	}
}