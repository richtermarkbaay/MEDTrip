<?php

namespace HealthCareAbroad\InstitutionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\Form\AbstractType;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\FormViewInterface;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionUserInvitation;

use HealthCareAbroad\InstitutionBundle\Entity\Institution;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('InstitutionBundle:Default:index.html.twig');
    }
    
    
    public function Accounts_Accept_Invitation($token,$id)
    {
    	$em = $this->getDoctrine()->getEntityManager();
    	
    	$Invitationtoken = $this->getDoctrine()->getRepository('HelperBundle:InvitationToken')
        			->find($token);
      
    	if (!$Invitationtoken) {
            throw $this->createNotFoundException('Invalid Token.');
        }
        
        $InstitutionUserInvitation = $em->getRepository('InstitutionBundle:InstitutionUserInvitation')
                       ->getId($id);
        
        return $this->render('BloggerBlogBundle:Blog:show.html.twig', array(
            'InstitutionUserInvitation'      => $InstitutionUserInvitation
        ));
    }
}
