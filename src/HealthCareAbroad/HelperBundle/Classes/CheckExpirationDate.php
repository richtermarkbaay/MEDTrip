<?php

namespace HealthCareAbroad\HelperBundle\Classes;
use HealthCareAbroad\HelperBundle\Entity\InvitationToken;
use Doctrine\ORM\EntityManager;

class CheckExpirationDate
{
	protected $doctrine;
	
	public function __construct(\Doctrine\Bundle\DoctrineBundle\Registry $doctrine)
	{
		$this->doctrine = $doctrine;
	}
	public function checkExpiredDateToken()
	{
		
		$expirationDate = new \DateTime('now');
		
		$repository = $this->doctrine
						   ->getRepository('HelperBundle:InvitationToken');
    	
		//select all token that has expired date and status is still active/1
		$query = $repository->createQueryBuilder('t')
    						->where('t.expiration_date < :expirationDate')
    						->add('where', 't.expirationDate < :expirationDate and t.status = 1')
    						->setParameter('expirationDate', $expirationDate)
    						->getQuery();

		return $token = $query->getResult();
		
	}
	public function inValidateToken()
	{
		$token = checkExpiredDateToken();
		if (!$token) {
        	throw $this->createNotFoundException('No token found with expirationDate '.$expirationDate);
    	}
    	else
    	{	
			for($i = 0; $i < count($token); $i++){
    			$token[$i]->setStatus('FALSE'); 
    		}
    		//$em = $this->getDoctrine()getEntityManager();
    		$em->flush();
    		return "Updated!";
    		return $response = inValidateToken($token,count($token));
    	}
		
	}
}