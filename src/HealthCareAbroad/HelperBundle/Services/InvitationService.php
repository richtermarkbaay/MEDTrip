<?php

namespace HealthCareAbroad\HelperBundle\Services;
use HealthCareAbroad\HelperBundle\Entity\InvitationToken;
use HealthCareAbroad\ProviderBundle\Entity\ProviderInvitation;
use ChromediaUtilities\Helpers\SecurityHelper;
use Doctrine\ORM\EntityManager;


class InvitationService
{
	protected $doctrine;
	
	/**
	 * 
	 * @var Doctrine\ORM\EntityManager
	 */
	protected $em;
	
	/**
	 * 
	 * @var \Twig_Environment
	 */
	protected $twig;
	
	protected $mailer;
	
	public function __construct(EntityManager $em)
	{
		$this->em = $em;
	}
	public function setTwig(\Twig_Environment $twig)
	{
	    $this->twig = $twig;
	}
	
	public function setMailer($mailer)
	{
	    $this->mailer = $mailer;
	}
	
	public function createInvitationToken($daysofExpiration)
	{
		$daysofExpiration = intVal($daysofExpiration);
		
		$generatedToken = SecurityHelper::hash_sha256(date('Ymdhms'));
		if(!$daysofExpiration){
			$daysofExpiration = 30;
		}
		
		$dateNow = new \DateTime('now');
		$expirationDate = $dateNow->modify('+'. $daysofExpiration .'days');
		$invitationToken = new InvitationToken();
		$invitationToken->setToken($generatedToken);
		$invitationToken->setExpirationDate($expirationDate);
		$invitationToken->setStatus("1");
		
 		$this->em->persist($invitationToken);
 		$this->em->flush();
 		return $invitationToken;
	}
	
	public function createProviderInvitation($email, $message, $name, $invitationToken)
	{
		$providerInvitation = new ProviderInvitation();
		$providerInvitation->setEmail($email);
		$providerInvitation->setMessage($message);
		$providerInvitation->setName($name);
		$providerInvitation->setStatus('1');
		$providerInvitation->setInvitationToken($invitationToken);
		
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
	    echo $messageBody; exit;

	    // send to email
	    $message = \Swift_Message::newInstance()

    	    ->setSubject('Provider User Invitation for Health Care Abroad')

    	    ->setFrom('chaztine.blance@chromedia.com')

    	    ->setTo($invitation->getEmail())

    	    ->setBody($messageBody);
	    
        return $this->mailer->send($message);
	}

}