<?php

namespace HealthCareAbroad\HelperBundle\Services;
use HealthCareAbroad\UserBundle\Entity\InstitutionUser;

use Doctrine\Tests\DBAL\Types\VarDateTimeTest;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionUserInvitation;
use HealthCareAbroad\InstitutionBundle\Entity\Institution;
use HealthCareAbroad\UserBundle\Entity\SiteUser;
use HealthCareAbroad\HelperBundle\Entity\InvitationToken;
use HealthCareAbroad\InstitutionBundle\Entity\InstitutionInvitation;

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
		$invitationToken->setStatus(SiteUser::STATUS_ACTIVE);
		
		$em = $this->doctrine->getEntityManager();
		$em->persist($invitationToken);
		$em->flush();
		return $invitationToken;
	}
	
	public function sendInstitutionInvitation(InstitutionInvitation $invitation)
	{	
		
		//generate token
		$token = $this->createInvitationToken(0);
		
		//create message
		$messageBody = $this->twig->render('InstitutionBundle:Email:institutionInvitation.html.twig', array(
				'name' => $invitation->getName(),
				'expirationDate' => $token->getExpirationDate(),
				'email' => $invitation->getEmail(),
				'token' => $token->getToken()
		));
		
		$message = \Swift_Message::newInstance()
			->setSubject('Activate your account with HealthCareAbroad')
			->setFrom('alnie.jacobe@chromedia.com')
			->setTo($invitation->getEmail())
			->setBody($messageBody);
		$sendResult = $this->mailer->send($message);
		
		$invitation->setMessage($message);
		$invitation->setInvitationToken($token);
		$invitation->setStatus(1);
		 
		$em = $this->doctrine->getEntityManager();
		$em->persist($invitation);
		$em->flush();
		
		return $sendResult;
	}
	
	//send email to institution user for his user and password
	public function sendInstitutionUserLoginCredentials(InstitutionUser $user, $password)
	{
		
		$messageBody = $this->twig->render('InstitutionBundle:Email:loginInformation.html.twig', array(
				'institutionName' => $user->getInstitution()->getName(),
				'firstName' => $user->getFirstName(),
				'email' => $user->getEmail(),
				'password' => $password
		));
		
		// send email to newly created chromedia accounts|institution user
		$message = \Swift_Message::newInstance()	
			->setSubject('Institution User Invitation for Health Care Abroad')
			->setFrom('alnie.jacobe@chromedia.com')
			->setTo($user->getEmail())
			->setBody($messageBody);
		 
		return $this->mailer->send($message);
	}
	
	public function sendInstitutionUserInvitation(Institution $institution, InstitutionUserInvitation $invitation)
	{
	    if (!$token = $invitation->getInvitationToken()) {
	        // generate a token
	        $token = $this->createInvitationToken(30);
	        $invitation->setInvitationToken($token);
	    }
	    $invitation->setInstitution($institution);
	    
	    $messageBody = $this->twig->render('InstitutionBundle:Email:invite.email.twig', array(
            'institutionUserInvitation' => $invitation,
            'token' => $token,
            'institution' => $institution
        ));

	    // send to email
	    $message = \Swift_Message::newInstance()
    	    ->setSubject('Institution User Invitation for Health Care Abroad')
    	    ->setFrom('chaztine.blance@chromedia.com')
    	    ->setTo($invitation->getEmail())
    	    ->setBody($messageBody);
	    $sendResult = $this->mailer->send($message);
	    
	    // persist invitation to database
	    $invitation->setStatus($sendResult ? InstitutionUserInvitation::STATUS_SENT : InstitutionUserInvitation::STATUS_PENDING_SENDING);
	    $em = $this->doctrine->getEntityManager();
	    $em->persist($invitation);
	    $em->flush();
	    
        return $sendResult;
	}

}