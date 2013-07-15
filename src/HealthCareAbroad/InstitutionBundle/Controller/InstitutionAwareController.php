<?php
/**
 * Base class for controllers that needs an instance of an Institution
 * 
 * @author Allejo Chris G. Velarde
 *
 */
namespace HealthCareAbroad\InstitutionBundle\Controller;

use HealthCareAbroad\UserBundle\Entity\SiteUser;

use HealthCareAbroad\InstitutionBundle\Entity\Institution;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

abstract class InstitutionAwareController extends Controller
{
    /**
     * @var Institution
     */
    protected $institution;
    
    protected $isSingleCenter;
    
    protected $eagerLoadEntities = array();
    
    /**
     * Convenience function to help controllers set up common variables
     */
    public function preExecute()
    {
        $institutionId = $this->getRequest()->getSession()->get('institutionId', 0);
        if ($institutionId) {

            // Temporarily eagerLoad in ProfileView
            if($this->getRequest()->attributes->get('_route') == 'institution_account_profile') {
                $institution = $this->get('services.institution')->getFullInstitutionById($institutionId);             
            } else {
                $institution = $this->get('services.institution.factory')->findById($institutionId);
            }

            if (!$institution) {
                $this->throwInvalidInstitutionException();
            }

            $this->setInstitution($institution);
        }
    }
    
    public function setInstitution(Institution $institution)
    {
        $this->institution = $institution;
        $this->isSingleCenter = $this->get('services.institution')->isSingleCenter($this->institution);
        $this->get('twig')->addGlobal('institution', $this->institution);
        $this->get('twig')->addGlobal('isSingleCenter', $this->isSingleCenter);
        $this->get('twig')->addGlobal('institutionLabel', $this->isSingleCenter ? 'Clinic' : 'Hospital');
        
//         if($this->get('security.context')->getToken()->getUser()){
//             $loggedUser = $this->get('security.context')->getToken()->getUser();
//             $this->get('twig')->addGlobal('userName',$loggedUser);
//         }
//         $this->get('twig')->addGlobal('userName', $loggedUser instanceof SiteUser ? $loggedUser->getFullName() : $loggedUser->getUsername());
    }
    
    public function throwInvalidInstitutionException()
    {
        throw $this->createNotFoundException("Invalid institution");
    }
    
    protected function jsonResponse()
    {
        
    }

    protected function setEagerLoadEntities($entitiesNames = array()) {
        $this->eagerLoadEntities = $entitiesNames;
    }
}