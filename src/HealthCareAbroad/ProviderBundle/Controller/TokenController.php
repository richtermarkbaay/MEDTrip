<?php

namespace HealthCareAbroad\ProviderBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use HealthCareAbroad\HelperBundle\Classes\Tokenizer;
use HealthCareAbroad\HelperBundle\Classes\CheckExpirationDate;
use HealthCareAbroad\HelperBundle\Entity\InvitationToken;

class TokenController extends Controller
{    

	// public function indexAction()
// 	{
// 		 return $this->render('ProviderBundle:Token:index.html.twig');
// 	}
	
    public function createAction(Request $request)
    {
    	$request = $this->getRequest();		
    	if ($request->getMethod() == 'POST') 
		{
    		$tokenizer = new Tokenizer();
    		$generatedToken = $tokenizer->generateTokenString();
    	
    		$expirationDate = new \DateTime('now');
    		$expirationDate->modify('+6 days');
			$status = "1";
			
    		$invitationToken = new InvitationToken();
    		$invitationToken->setToken($generatedToken);
    		$invitationToken->setExpirationDate($expirationDate);
    		$invitationToken->setStatus($status);
    		$em = $this->getDoctrine()->getEntityManager();
    		$em->persist($invitationToken);
    		$em->flush();
    		
    		//$checkToken = $this->get('CheckExpirationDate')->checkExpiredDateToken();
    		return new Response('Created token! and '.$checkToken .' token with Expired dates');
    		
    		//send email
    		
    	}
    	return $this->render('ProviderBundle:Token:create.html.twig');
    }
    /*public function checkAction(Request $request)
    {
    	//$generatedToken = $checkToken->checkforExpirationDate("2012-07-15 08:20:38");
    	$request = $this->getRequest();		
    	if ($request->getMethod() == 'POST') 
		{
			//$expirationDate = date_create(date('2012-07-15 08:20:38'));
			$expirationDate = new \DateTime('now');
			$repository = $this->getDoctrine()
    						   ->getRepository('HelperBundle:InvitationToken');
    						   
			//select all token that has expired date and status is still active/1
			$query = $repository->createQueryBuilder('t')
    							->where('t.expiration_date < :expirationDate')
    							->add('where', 't.expirationDate < :expirationDate and t.status = 1')
    							->setParameter('expirationDate', $expirationDate)
    							->getQuery();

			$token = $query->getResult();    		
			if (!$token) {
        		throw $this->createNotFoundException('No token found with expirationDate '.$expirationDate);
    		}
    		else
    		{	
    			for($i = 0; $i < count($token); $i++){
    				$token[$i]->setStatus('FALSE'); 
    			}
    			$em = $this->getDoctrine()->getEntityManager();
    			$em->flush();
    			return new Response("Updated!");
    		}
    		
		}  
    	return $this->render('ProviderBundle:Token:check.html.twig');
    }*/
    
}