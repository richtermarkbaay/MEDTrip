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
    
    /**
     * Convenience function to help controllers set up common variables
     */
    public function preExecute()
    {
        
    }
    
    public function setInstitution(Institution $institution)
    {
        $this->institution = $institution;
        //$this->get('twig')->addGlobal('institution', $this->institution);
        //$this->get('twig')->addGlobal('isSingleCenter', $this->get('services.institution')->isSingleCenter($this->institution));
        $loggedUser = $this->get('security.context')->getToken()->getUser();
        //$this->get('twig')->addGlobal('userName', $loggedUser instanceof SiteUser ? $loggedUser->getFullName() : $loggedUser->getUsername());
    }
    
    public function throwInvalidInstitutionException()
    {
        throw $this->createNotFoundException("Invalid institution");
    }
    
    protected function jsonResponse()
    {
        
    }
}