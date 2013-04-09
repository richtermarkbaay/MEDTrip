<?php
namespace HealthCareAbroad\InstitutionBundle\Services;
use HealthCareAbroad\InstitutionBundle\Entity\InstitutionSignupStepStatus;

/**
 * Service class to handle the flow of institution sign up which consists of several steps and different flows depending on the institution type
 * 
 * @author Allejo Chris G. Velarde
 *
 */
use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter;

use HealthCareAbroad\InstitutionBundle\Entity\Institution;

class SignUpService
{
    const COMPLETE_PROFILE_INFORMATION = 1;
    
    const ADD_SPECIALIZATIONS_AND_TREATMENTS = 2;
    
    const ADD_ANCILLIARY_SERVICES = 3;
    
    /**
     * @var InstitutionFactory
     */
    private $institutionFactory;
    
    /**
     * @var InstitutionMedicalCenterService
     */
    private $institutionMedicalCenterService;
    
    public function __construct()
    {
        
    }
    
    public function setInstitutionFactory(InstitutionFactory $factory)
    {
        $this->institutionFactory = $factory;    
    }
    
    public function setInstitutionMedicalCenterService(InstitutionMedicalCenterService $service)
    {
        $this->institutionMedicalCenterService = $service;
    }
    
    public function completeProfileOfInstitutionWithSingleCenter(Institution $institution, InstitutionMedicalCenter $institutionMedicalCenter)
    {
        // save the institution
        $institution->setSignupStepStatus(InstitutionSignupStepStatus::STEP2);
        $this->institutionFactory->save($institution);
        
        // set medical center name and description to institution.name and institution.description
        $institutionMedicalCenter->setName($institution->getName());
        $institutionMedicalCenter->setDescription($institution->getDescription() ? $institution->getDescription() : '2');
        $institutionMedicalCenter->setInstitution($institution);
        $institutionMedicalCenter->setAddress($institution->getAddress1());
        $institutionMedicalCenter->setCoordinates($institution->getCoordinates());
        
        // save institution medical center as draft
        $this->institutionMedicalCenterService->saveAsDraft($institutionMedicalCenter);
    }
    
    public function completeProfileOfInstitutionWithMultipleCenter(Institution $institution)
    {
        $institution->setSignupStepStatus(InstitutionSignupStepStatus::FINISH);
        $this->institutionFactory->save($institution);
    }
}

