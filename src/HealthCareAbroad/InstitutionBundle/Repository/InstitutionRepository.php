<?php

namespace HealthCareAbroad\InstitutionBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * InstitutionRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class InstitutionRepository extends EntityRepository
{
	public function search($term = '', $limit = 10)
	{
		$dql = "
			SELECT i
			FROM InstitutionBundle:Institution AS i
			WHERE i.name LIKE :term
			ORDER BY i.name ASC"
		;
	
		$query = $this->_em->createQuery($dql);
		$query->setParameter('term', "%$term%");
		$query->setMaxResults($limit);
	
		return $query->getResult();
	}	
}