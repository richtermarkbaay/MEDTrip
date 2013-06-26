<?php

namespace HealthCareAbroad\InstitutionBundle\Controller;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionTypes;

use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

use HealthCareAbroad\UserBundle\Entity\AdminUser;

use Symfony\Component\HttpFoundation\Request;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Dedicated controller class for authenticating users that will use Institution bundle
 * 
 * @author Allejo Chris G. Velarde
 *
 */
class AuthenticationController extends Controller
{
    public function internalAdminLoginBypassAction(Request $request)
    {
        $adminUser = $this->getUser();
        if ($adminUser instanceof AdminUser) {
            $institution = $this->getDoctrine()->getRepository('InstitutionBundle:Institution')->find($request->get('institutionId', 0));
            if (!$institution) {
                throw $this->createNotFoundException('Invalid institution for admin internal login');
            }
            $session = $request->getSession();
            $session->set('accountId', $adminUser->getAccountId());
            $session->set('institutionId', $institution->getId());
            $session->set('institutionName', $institution->getName());
            $session->set('isSingleCenterInstitution', InstitutionTypes::SINGLE_CENTER == $institution->getType());
            $session->set('institutionSignupStepStatus', $institution->getSignupStepStatus());
            $session->set('userFirstName', $adminUser->getFirstName());
            $session->set('userLastName', $adminUser->getLastName());
            $session->set('userEmail', $adminUser->getEmail());
            
            return $this->redirect($this->generateUrl('institution_homepage'));
        }
        else {
            throw new AccessDeniedHttpException('Unauthorized Non internal admin');
        }
        
    } 
}