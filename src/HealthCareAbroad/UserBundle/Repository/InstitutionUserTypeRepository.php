<?php

namespace HealthCareAbroad\UserBundle\Repository;
use HealthCareAbroad\UserBundle\Entity\InstitutionUserType;
use Doctrine\ORM\EntityRepository;

/**
 * InstitutionUserTypeRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class InstitutionUserTypeRepository extends EntityRepository
{
	/**
	 * Get all admin user types that are editable
	 */
	public function getAllEditable($institutionId)
	{
		$dql = "SELECT a FROM UserBundle:InstitutionUserType a WHERE a.institution = :institutionId AND a.status = :active OR a.status = :inactive ";
		
		$query = $this->getEntityManager()->createQuery($dql)
		->setParameter('active', InstitutionUserType::STATUS_ACTIVE)
		->setParameter('inactive', InstitutionUserType::STATUS_INACTIVE)
		->setParameter('institutionId', $institutionId);
		return $query->getResult();
	}
}