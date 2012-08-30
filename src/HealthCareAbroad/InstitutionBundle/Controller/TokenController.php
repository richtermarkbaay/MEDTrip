<?php

namespace HealthCareAbroad\InstitutionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use HealthCareAbroad\InstitutionBundle\Form\InstitutionInvitationType;
use HealthCareAbroad\HelperBundle\Entity\InvitationToken;
use HealthCareAbroad\InstitutionBundle\Entity\InstitutionInvitation;

class TokenController extends Controller
{

	public function confirmInvitationTokenAction()
    {    
    	//get token	 
    	$token = $this->getRequest()->get('token',null);
		$invitation = $this->get('services.token')->getActiveInstitutionInvitationByToken($token);	
    	if (!$invitation) {
            throw $this->createNotFoundException('Invalid token');
        }
        
        $this->get('session')->setFlash('success', "Successfully confirm token!");
		return $this->render('InstitutionBundle:Token:confirmInvitationToken.html.twig', array('token' => $token));
    }
	
}