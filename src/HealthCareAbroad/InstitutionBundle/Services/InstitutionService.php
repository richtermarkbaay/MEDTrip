<?php
/**
 * Service class for Institution
 * 
 * @author Allejo Chris G. Velarde
 * @author Alnie Jacobe
 */
namespace HealthCareAbroad\InstitutionBundle\Services;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionStatus;

use HealthCareAbroad\UserBundle\Services\InstitutionUserService;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionUserInvitation;

use HealthCareAbroad\InstitutionBundle\Entity\Institution;
use HealthCareAbroad\HelperBundle\Entity\City;
use HealthCareAbroad\HelperBundle\Entity\Country;
class InstitutionService
{
	
    protected $doctrine;
    
    /**
     * @var HealthCareAbroad\UserBundle\Services\InstitutionUserService
     */
    protected $institutionUserService;
    
    public function __construct(\Doctrine\Bundle\DoctrineBundle\Registry $doctrine )
    {
    	$this->doctrine = $doctrine;
    }
    
    public function setInstitutionUserService(InstitutionUserService $institutionUserService)
    {
        $this->institutionUserService = $institutionUserService;
    }
    
    
    public function getAllStaffOfInstitution(Institution $institution)
    {
        $users = $this->doctrine->getRepository('UserBundle:InstitutionUser')->findByInstitution($institution);
        
        $returnValue = array();
        foreach($users as $user) {
            $returnValue[] = $this->institutionUserService->getAccountData($user);
        }
        return $returnValue;
    }
}