<?php

namespace HealthCareAbroad\HelperBundle\Services;

use HealthCareAbroad\HelperBundle\Entity\InvitationToken;
use HealthCareAbroad\ProviderBundle\Entity\ProviderInvitation;

class TokenService
{
	protected $doctrine;
	
	public function __construct(\Doctrine\Bundle\DoctrineBundle\Registry $doctrine)
	{
		$this->doctrine = $doctrine;
	}
	
	public function validate($token)
	{
		$repository = $this->doctrine->getRepository('HelperBundle:InvitationToken');
 						   
		//select all token that has expired date and status is still active/1
		$query = $repository->createQueryBuilder('t')
 							->select('t.id')
     						->add('where', 't.token = :token and t.status = 1')
     						->setParameter('token', $token)
     						->getQuery();
     	$invitationToken = $query->getResult();
		
        if(count($invitationToken) > 0){
			
			$invitationTokenId =  $invitationToken[0]["id"]; 
			//retrieve data esp name,email
			$repository = $this->doctrine
         						   ->getRepository('ProviderBundle:ProviderInvitation');
         						   
        	$query	= $repository->createQueryBuilder('p')
 								 ->add('where', 'p.invitationToken = :token_id and p.status = 1')
     							 ->setParameter('token_id', $invitationTokenId)
     							 ->getQuery();
     		$providerInvitation = $query->getResult();
     		return $providerInvitation;
    	}
		else{
			return $invitationToken; 
		}
	}
	
	/**
	 * Validate if the string token is still valid
	 *
	 * @param string $token
	 * @return NULL | HealthCareAbroad\ProviderBundle\Entity\ProviderUserInvitation
	 */
	public function getActiveProviderUserInvitatinByToken($token)
	{
	    // find a valid token
	    $dql = "SELECT a FROM ProviderBundle:ProviderUserInvitation a JOIN a.invitationToken b WHERE b.token = :token AND b.status=:token_status";
	    $providerInvitation = $this->doctrine->getEntityManager()->createQuery($dql)
	        ->setParameter('token', $token)
	        ->setParameter('token_status', 1)
	        ->getOneOrNullResult();
	    
	    return $providerInvitation;
	}
}