<?php
namespace HealthCareAbroad\InstitutionBundle\Services;
use HealthCareAbroad\InstitutionBundle\Entity\SignUpStep;

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
//     const COMPLETE_PROFILE_INFORMATION = 1;
    
//     const ADD_SPECIALIZATIONS_AND_TREATMENTS = 2;
    
//     const ADD_ANCILLIARY_SERVICES = 3;
    
    CONST MULTIPLE_CENTER_SIGN_UP = 'multiple_center';
    
    const SINGLE_CENTER_SIGN_UP = 'single_center';
    
    /**
     * @var InstitutionFactory
     */
    private $institutionFactory;
    
    /**
     * @var InstitutionMedicalCenterService
     */
    private $institutionMedicalCenterService;
    
    /**
     * Array of steps with sign up context as collection key
     * 
     * @var array
     */
    private $signUpSteps = array();
    
    /**
     * Array of steps with sign up context as collection key, each collection has route as key
     *
     * @var array
     */
    private $signUpStepsByRoute = array();
    
    public function __construct()
    {
        
    }
    
    public function setSignUpSteps(array $stepsConfig)
    {
        $knownContexts = array(SignUpService::MULTIPLE_CENTER_SIGN_UP, SignUpService::SINGLE_CENTER_SIGN_UP);
        foreach ($stepsConfig as $_signUpContext => $_steps) {
            
            if (!\in_array($_signUpContext, $knownContexts)) {
                // unknown sign up context
                continue;
            }
            
            $parent = null;
            foreach ($_steps as $key => $v) {
                $_step = new SignUpStep();
                $_step->setLabel($v['label']);
                $_step->setRoute($v['route']);
                // keys in array will start in 0
                $_step->setStepNumber($key+1);
                $_step->setSub(isset($v['sub']) ? $v['sub'] : false);
                
                // if this step is a sub step, set the parent to the recent $parent
                // NOTE: this implementation is restricted to the arrangement in the steps configuration
                if ($_step->isSub()) {
                    $_step->setParent($parent);
                }
                else {
                    // step is not a sub, set as the current parent
                    $parent = $_step;
                }
                
                $this->signUpSteps[$_signUpContext][] = $_step;
                $this->signUpStepsByRoute[$_signUpContext][$_step->getRoute()] = $_step;
            }
        }
    }
    
    public function setInstitutionFactory(InstitutionFactory $factory)
    {
        $this->institutionFactory = $factory;    
    }
    
    public function setInstitutionMedicalCenterService(InstitutionMedicalCenterService $service)
    {
        $this->institutionMedicalCenterService = $service;
    }
    
    /**
     * Get sign up steps of a Multiple Center sign up
     * 
     * @return array SignUpStep
     */
    public function getMultipleCenterSignUpSteps()
    {
        return $this->signUpSteps[SignUpService::MULTIPLE_CENTER_SIGN_UP];
    }
    
    public function getMultipleCenterSignUpStepByRoute($route=null)
    {
        if ('' == trim($route)) {
            return null;
        }
    
        return \array_key_exists($route, $this->signUpStepsByRoute[SignUpService::MULTIPLE_CENTER_SIGN_UP])
        ? $this->signUpStepsByRoute[SignUpService::MULTIPLE_CENTER_SIGN_UP][$route]
        : null;
    }
    
    /**
     * Get the next SignUpStep from the passed $step
     * 
     * @param SignUpStep $step
     * @return SignUpStep
     */
    public function getMultipleCenterSignUpNextStep(SignUpStep $step)
    {
        // next step's array key will be $step's stepNumber since our arrays starts with index 0
        return isset($this->signUpSteps[SignUpService::MULTIPLE_CENTER_SIGN_UP][$step->getStepNumber()])
            ? $this->signUpSteps[SignUpService::MULTIPLE_CENTER_SIGN_UP][$step->getStepNumber()]
            : null;
    }
    
    public function getSingleCenterSignUpSteps()
    {
        return $this->signUpSteps[SignUpService::SINGLE_CENTER_SIGN_UP];
    }
    
    public function getSingleCenterSignUpStepByRoute($route=null)
    {
        if ('' == trim($route)) {
            return null;
        }
    
        return \array_key_exists($route, $this->signUpStepsByRoute[SignUpService::SINGLE_CENTER_SIGN_UP])
        ? $this->signUpStepsByRoute[SignUpService::SINGLE_CENTER_SIGN_UP][$route]
        : null;
    }
    
    public function getSingleCenterSignUpNextStep(SignUpStep $step)
    {
        // next step's array key will be $step's stepNumber since our arrays starts with index 0
        return isset($this->signUpSteps[SignUpService::SINGLE_CENTER_SIGN_UP][$step->getStepNumber()])
            ? $this->signUpSteps[SignUpService::SINGLE_CENTER_SIGN_UP][$step->getStepNumber()]
            : null;
    }
    
    public function completeProfileOfInstitutionWithSingleCenter(Institution $institution, InstitutionMedicalCenter $institutionMedicalCenter)
    {
        // save the institution
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
        $this->institutionFactory->save($institution);
    }
}

