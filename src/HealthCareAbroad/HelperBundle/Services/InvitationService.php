<?php

namespace HealthCareAbroad\HelperBundle\Services;
use HealthCareAbroad\ProviderBundle\Entity\ProviderUserInvitation;

use HealthCareAbroad\ProviderBundle\Entity\Provider;

use HealthCareAbroad\HelperBundle\Entity\InvitationToken;
use HealthCareAbroad\ProviderBundle\Entity\ProviderInvitation;
use ChromediaUtilities\Helpers\SecurityHelper;
use Doctrine\ORM\EntityManager;


class InvitationService
{
	protected $doctrine;
	
	/**
	 * 
	 * @var EntityManager
	 */
	protected $em;
	
	/**
	 * 
	 * @var \Twig_Environment
	 */
	protected $twig;
	
	public function __construct(EntityManager $em)
	{
		$this->em = $em;
	}
	
	public function setTwig(\Twig_Environment $twig)
	{
	    $this->twig = $twig;
	}
	
	public function createInvitationToken($expirationDate)
	{
		$generatedToken = SecurityHelper::hash_sha256(date('Ymdhms'));
		
		$invitationToken = new InvitationToken();
		$invitationToken->setToken($generatedToken);
		$invitationToken->setExpirationDate(new \DateTime('+30 days'));
		$invitationToken->setStatus("1");
		
 		$this->em->persist($invitationToken);
 		$this->em->flush();
 		
 		return $invitationToken;
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
	
	public function sendProviderUserInvitation(Provider $provider, ProviderUserInvitation $invitation)
	{
	    if (!$token = $invitation->getInvitationToken()) {
	        // generate a token
	        $token = $this->createInvitationToken(30);
	        $invitation->setInvitationToken($token);
	    }
	    
	    // persist invitation to database
	    $this->em->persist($invitation);
	    $this->em->flush();
	    $messageBody = $this->twig->render('ProviderBundle:Email:invite.email.twig', array(
            'providerUserInvitation' => $invitation,
            'token' => $token,
            'provider' => $provider
        ));
	    
	    // send to email
	    $message = \Swift_Message::newInstance()
	    ->setSubject('Provider User Invitation for Health Care Abroad')
	    ->setFrom('chaztine.blance@chromedia.com')
	    ->setTo($invitation->getEmail())
	    ->setBody($messageBody);
	    
	    //$this->mailer->send($message);
	    
	    
	    return true;
	}
}