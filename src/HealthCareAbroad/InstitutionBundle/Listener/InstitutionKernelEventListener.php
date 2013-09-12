<?php
/**
 * @author Allejo Chris G. Velarde
 */
namespace HealthCareAbroad\InstitutionBundle\Listener;

use Symfony\Component\HttpFoundation\RedirectResponse;

use Symfony\Bundle\FrameworkBundle\Routing\Router;

use HealthCareAbroad\InstitutionBundle\Services\InstitutionService;

use Symfony\Component\HttpKernel\HttpKernel;

use HealthCareAbroad\InstitutionBundle\Services\SignUpService;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;

use Doctrine\Bundle\DoctrineBundle\Registry;

use HealthCareAbroad\InstitutionBundle\Controller\InstitutionAwareController;

use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

class InstitutionKernelEventListener
{
    /**
     * @var Registry
     */
    private $doctrine;
    
    /**
     * @var InstitutionService
     */
    private $institutionService;
    
    /**
     * @var SignUpService
     */
    private $institutionSignUpService;
    
    /**
     * @var Router
     */
    private $router;
    
    public function setDoctrine(Registry $doctrine)
    {
        $this->doctrine = $doctrine;
    }
    
    public function setInstitutionService(InstitutionService $v)
    {
        $this->institutionService = $v;
    }
    
    public function setInstitutionSignUpService(SignUpService $v)
    {
        $this->institutionSignUpService = $v;
    }
    
    public function setRouter(Router $v)
    {
        $this->router = $v;
    }
    
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (HttpKernel::MASTER_REQUEST != $event->getRequestType()) {
            // not a master request, do nothing
            return;
        }
        
        $request = $event->getRequest();
        $session = $request->getSession();
        $matchedRoute = $request->get('_route');

        if (!\preg_match('/^\/institution/', $request->getPathInfo())) {
            return false;
        }

        // TODO: Quick fix only. We may want to check security context here instead
        if ($matchedRoute == 'institution_login' || in_array($matchedRoute, $this->_getAllowedSignupRoutes())) {
            return;
        }
        
        // validate sign up flow status if it's not complete yet
        if (SignUpService::COMPLETED_SIGNUP_FLOW_STATUS != $session->get('institutionSignupStepStatus') ) {
            $response = $this->validateSignUpStatus($session, $matchedRoute);
            if ($response) {
                $event->setResponse($response);
                return;
            }
        }
        
    }
    
    private function validateSignUpStatus($session, $currentRoute)
    {
        $response = null;
        $signupStepStatus = $session->get('institutionSignupStepStatus');
        
        // check if the institution has completed sign up flow
        if($session->get('isSingleCenterInstitution')) {
            $lastStep = $this->institutionSignUpService->getSingleCenterSignUpLastStep();
            $nextStep = $this->institutionSignUpService->getSingleCenterSignUpNextStep($signupStepStatus);
        }
        else {
            $lastStep = $this->institutionSignUpService->getMultipleCenterSignUpLastStep();
            $nextStep = $this->institutionSignUpService->getMultipleCenterSignUpNextStep($signupStepStatus);
        }
        
        // has not completed institution sign up flow yet
        if ($lastStep && $signupStepStatus < $lastStep->getStepNumber()) {
            $routeName = $nextStep->getRoute();
            
            // we don't need to redirect anymore
            if ($routeName != $currentRoute) {
                $routeParams = array();
                // only steps other than step 1 has imcId parameter
                if ($signupStepStatus > 1) {
                    $medicalCenter = $this->institutionService->getFirstMedicalCenterByInstitutionId($session->get('institutionId'));
                    $routeParams = array('imcId' => $medicalCenter ?  $medicalCenter->getId() : 0);
                }
                
                // redirect to incomplete step in signup flow
                $response = new RedirectResponse($this->router->generate($routeName, $routeParams));
            }
        }
        
        return $response;
    }
    
    private function _getAllowedSignupRoutes() {

        return array('institution_medicalCenter_ajaxUpdateDoctor', 'institution_medicalCenter_removeDoctor', 'institution_signup_finish', 'institution_medicalCenter_addExistingDoctor', 'institution_uploadLogo', 'institution_uploadFeaturedImage', 'institution_doctor_logo_upload');
    }
}