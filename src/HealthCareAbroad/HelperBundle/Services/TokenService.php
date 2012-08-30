<?php

namespace HealthCareAbroad\HelperBundle\Services;

use HealthCareAbroad\HelperBundle\Entity\InvitationToken;
use HealthCareAbroad\InstitutionBundle\Entity\InstitutionInvitation;

class TokenService
{
	protected $doctrine;
	
	public function __construct(\Doctrine\Bundle\DoctrineBundle\Registry $doctrine)
	{
		$this->doctrine = $doctrine;
	}
	
	
	protected function getQueryForValidToken($entity, $token)
	{
		// find a valid token
		
		$dql = "SELECT a FROM ". $entity ." a JOIN a.invitationToken b WHERE b.token = :token AND b.status=:token_status";
		$query = $this->doctrine->getEntityManager()->createQuery($dql)
			->setParameter('token', $token)
			->setParameter('token_status', 1);
		return $query;		
	}
	
	/**
	 * Validate if the string token is still valid
	 *
	 * @param string $token
	 * @return NULL | HealthCareAbroad\InstitutionBundle\Entity\InstitutionInvitation
	 */
	public function getActiveInstitutionInvitationByToken($token)
	{
		return $this->getQueryForValidToken('InstitutionBundle:InstitutionInvitation', $token)
		->getOneOrNullResult();
	}
	
	/**
	 * Validate if the string token is still valid
	 *
	 * @param string $token
	 * @return NULL | HealthCareAbroad\InstitutionBundle\Entity\InstitutionUserInvitation
	 */
	public function getActiveInstitutionUserInvitationByToken($token)
	{
	    return $this->getQueryForValidToken('InstitutionBundle:InstitutionUserInvitation', $token)
			->getOneOrNullResult();
	}
}