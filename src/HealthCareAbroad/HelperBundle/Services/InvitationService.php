<?php

namespace HealthCareAbroad\HelperBundle\Services;
use HealthCareAbroad\UserBundle\Entity\ProviderUser;

use Doctrine\Tests\DBAL\Types\VarDateTimeTest;

use HealthCareAbroad\ProviderBundle\Entity\ProviderUserInvitation;

use HealthCareAbroad\ProviderBundle\Entity\Provider;

use HealthCareAbroad\HelperBundle\Entity\InvitationToken;
use HealthCareAbroad\ProviderBundle\Entity\ProviderInvitation;
use ChromediaUtilities\Helpers\SecurityHelper;
use Doctrine\ORM\EntityManager;


class InvitationService
{
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
	protected $doctrine;
	
	public function __construct(\Doctrine\Bundle\DoctrineBundle\Registry $doctrine )
	{
		$this->doctrine = $doctrine;
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
		
		//generate token
		$generatedToken = SecurityHelper::hash_sha256(date('Ymdhms'));
		
		//check if expiration days given is 0|less than 0
		if ($daysofExpiration <= 0) {
			$daysofExpiration = 30;
		}
		
		//generate expiration date
		$dateNow = new \DateTime('now');
		$expirationDate = $dateNow->modify('+'. $daysofExpiration .' days');
		
		$invitationToken = new InvitationToken();
		$invitationToken->setToken($generatedToken);
		$invitationToken->setExpirationDate($expirationDate);
		$invitationToken->setStatus("1");
		
		$em = $this->doctrine->getEntityManager();
		$em->persist($invitationToken);
		$em->flush();
		// failed to save
		if (!$invitationToken) {
			return $this->_errorResponse(500, 'Exception encountered upon persisting data.');
		}
		
 		return $invitationToken;
	}
	
	public function createProviderInvitation(ProviderInvitation $invitation, $message, InvitationToken $token)
	{	
		$invitation->setMessage($message);
		$invitation->setStatus('0');
		$invitation->setInvitationToken($token);
		
		$em = $this->doctrine->getEntityManager();
		$em->persist($invitation);
		$em->flush();
		
		// failed to save
		if (!$invitation) {
			return $this->_errorResponse(500, 'Exception encountered upon persisting data.');
		}
		
		return $invitation;
	}
	
	//send email to provider user for his user and password
	public function sendProviderUserLoginCredentials(ProviderUser $user, $password)
	{
		
		$messageBody = $this->twig->render('ProviderBundle:Email:loginInformation.html.twig', array(

				'providerName' => $user->getProvider()->getName(),
				
				'firstName' => $user->getFirstName(),
		
				'email' => $user->getEmail(),
		
				'password' => $password
				
		));
		
		// send email to newly created chromedia accounts|provider user
		$message = \Swift_Message::newInstance()
	
		->setSubject('Provider User Invitation for Health Care Abroad')
	
		->setFrom('alnie.jacobe@chromedia.com')
	
		->setTo($user->getEmail())
	
		->setBody($messageBody);
		 
		return $this->mailer->send($message);
	}
	
	public function sendProviderUserInvitation(Provider $provider, ProviderUserInvitation $invitation)
	{
	    if (!$token = $invitation->getInvitationToken()) {

	        // generate a token

	        $token = $this->createInvitationToken(30);
	        $invitation->setInvitationToken($token);

	    }
	    
	    // persist invitation to database
	    $em = $this->doctrine->getEntityManager();
	    $em->persist($invitation);
	    $em->flush();
	    $messageBody = $this->twig->render('ProviderBundle:Email:invite.email.twig', array(
            'providerUserInvitation' => $invitation,

            'token' => $token,

            'provider' => $provider
        ));
	    //echo $messageBody; exit;

	    // send to email
	    $message = \Swift_Message::newInstance()

    	    ->setSubject('Provider User Invitation for Health Care Abroad')

    	    ->setFrom('chaztine.blance@chromedia.com')

    	    ->setTo($invitation->getEmail())

    	    ->setBody($messageBody);
	    
        return $this->mailer->send($message);
	}

}