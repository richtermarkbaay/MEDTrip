<?php

namespace HealthCareAbroad\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use HealthCareAbroad\InstitutionBundle\Entity\Institution;

use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\HttpFoundation\Request;

class InstitutionUserController extends Controller
{
    /**
     * @var Institution
     */
    private $institution;
    
    public function preExecute()
    {
        $this->institution = $this->get('services.institution.factory')->findById($this->getRequest()->get('institutionId'));
        if (!$this->institution) {
            throw $this->createNotFoundException('Invalid institution');
        }
    }
    /**
     * 
     * @param Request $request
     */    
    public function ajaxLoadAdminUsersAction(Request $request)
    {
        $users = $this->get('services.institution')->getAdminUsers($this->institution);
        $html = $this->renderView('AdminBundle:InstitutionUser:ajaxLoadAdminUsers.html.twig', array(
            'institution' => $this->institution,
            'adminUsers' => $users
        ));
        
        $response = new Response(\json_encode(array('html' => $html)), 200, array('content-type' => 'application/json'));
        
        return $response;
    }
    
    public function bypassClientAdminLoginAction(Request $request)
    {
        $user = $this->get('services.institution_user')->findById($request->get('accountId'));
        if ($this->institution->getId() != $user->getInstitution()->getId() ) {
            return new Response(\sprintf('User %s does not belong to institution %s', $user->getAccountId(), $this->institution->getId()), 401);
        }
        return $this->render('AdminBundle:InstitutionUser:bypassClientAdminLogin.html.twig', array(
            'user' => $user,
            'institution' => $this->institution));
    }
}