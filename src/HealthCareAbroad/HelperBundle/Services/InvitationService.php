<?php

namespace HealthCareAbroad\HelperBundle\Services;
use HealthCareAbroad\HelperBundle\Entity\InvitationToken;
use HealthCareAbroad\ProviderBundle\Entity\ProviderInvitation;
use ChromediaUtilities\Helpers\SecurityHelper;
use Doctrine\ORM\EntityManager;


class InvitationService
{
	protected $doctrine;
	
	public function __construct(EntityManager $em)
	{
		$this->em = $em;
	}
	public function createInvitationToken($expirationDate)
	{
		$generatedToken = SecurityHelper::hash_sha256(date('Ymdhms'));
		
		$invitationToken = new InvitationToken();
		$invitationToken->setToken($generatedToken);
		$invitationToken->setExpirationDate($expirationDate);
		$invitationToken->setStatus("1");
		
 		$this->em->persist($invitationToken);
 		$this->em->flush();
 		return $generatedToken;
	}
	
	
	public function createProviderInvitation($email, $message, $name)
	{
		$providerInvitation = new ProviderInvitation();
		$providerInvitation->setEmail($email);
		$providerInvitation->setMessage($message);
		$providerInvitation->setName($name);
		$providerInvitation->setStatus('1');
		//$providerInvitation->setInvitationToken($generatedToken);
		
		$this->em->persist($providerInvitation);
		$this->em->flush();
		
	}	
}