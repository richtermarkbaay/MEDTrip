<?php

namespace HealthCareAbroad\ProviderBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\Form\AbstractType;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\FormViewInterface;

use HealthCareAbroad\ProviderBundle\Entity\ProviderUserInvitation;

use HealthCareAbroad\ProviderBundle\Entity\Provider;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('ProviderBundle:Default:index.html.twig');
    }
    
    
    public function Accounts_Accept_Invitation($token,$id)
    {
    	$em = $this->getDoctrine()->getEntityManager();
    	
    	$Invitationtoken = $this->getDoctrine()->getRepository('HelperBundle:InvitationToken')
        			->find($token);
      
    	if (!$Invitationtoken) {
            throw $this->createNotFoundException('Invalid Token.');
        }
        
        $ProviderUserInvitation = $em->getRepository('ProviderBundle:ProviderUserInvitation')
                       ->getId($id);
        
        return $this->render('BloggerBlogBundle:Blog:show.html.twig', array(
            'ProviderUserInvitation'      => $ProviderUserInvitation
        ));
    }
}
