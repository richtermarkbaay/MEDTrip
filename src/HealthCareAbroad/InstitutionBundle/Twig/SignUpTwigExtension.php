<?php

namespace HealthCareAbroad\InstitutionBundle\Twig;

use HealthCareAbroad\InstitutionBundle\Entity\SignUpStep;

use HealthCareAbroad\InstitutionBundle\Services\SignUpService;

use HealthCareAbroad\InstitutionBundle\Entity\Institution;

class SignUpTwigExtension extends \Twig_Extension
{
    /**
     * @var SignUpService
     */
    private $signUpService;
    
    /**
     * @var \Twig_Environment
     */
    private $twig;
    
    public function getName()
    {
        return 'institution_signup_twig_extension';
    }
    
    public function getFunctions()
    {
        return array(
            'render_signup_steps_by_route' => new \Twig_Function_Method($this, 'render_signup_steps_by_route'),
        );
    }
    
    public function setTwig(\Twig_Environment $v)
    {
        $this->twig = $v;
    }
    
    /**
     * @param SignUpService $v
     */
    public function setInstitutionSignUpService(SignUpService $v)
    {
        $this->signUpService = $v;
    }
    
    
    public function render_signup_steps_by_route($route, $isSingleCenter=false)
    {
//         $isSingleCenter = true;
//         $route = 'institution_signup_setup_institutionDoctors';
        if ('institution_signUp' == $route) {
            // we are still in initial sign up, so no specific steps yet
            $template = $this->twig->render('InstitutionBundle:SignUp/Widgets:steps.initial.html.twig');
        }
        else {
            if ($isSingleCenter) {
                $steps = $this->signUpService->getSingleCenterSignUpSteps();
                $currentStep = $this->signUpService->getSingleCenterSignUpStepByRoute($route);
            }
            else {
                $steps = $this->signUpService->getMultipleCenterSignUpSteps();
                $currentStep = $this->signUpService->getMultipleCenterSignUpStepByRoute($route);
            }
            
            $template = $this->twig->render('InstitutionBundle:SignUp/Widgets:steps.html.twig', array(
                'steps' => $steps,
                'currentStep' => $currentStep ? $currentStep : new SignUpStep()
            ));
        }
        
        return $template;
    }
}