<?php
namespace HealthCareAbroad\UserBundle\Handler;

use HealthCareAbroad\InstitutionBundle\Services\InstitutionMedicalCenterService;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionSignupStepStatus;

use Symfony\Bundle\FrameworkBundle\Routing\Router;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
//use Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;

class InstitutionAuthenticationHandler implements AuthenticationSuccessHandlerInterface, AuthenticationFailureHandlerInterface
{
    protected $router;
    protected $callouts;
    protected $medicalCenterService;

    public function __construct(Router $router, InstitutionMedicalCenterService $medicalCenterService, $callouts)
    {
        $this->router = $router;
        $this->callouts = $callouts;
        $this->medicalCenterService = $medicalCenterService;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    { 
        $routeParams = array();
        $routeName = 'institution_homepage';

        $session = $request->getSession();
        $calloutMessage = $this->callouts['login_complete_profile'];
        $signupStepStatus = $session->get('institutionSignupStepStatus');

        if(!InstitutionSignupStepStatus::hasCompletedSteps($signupStepStatus)) {
            if($session->get('isSingleCenterInstitution')) {
                $calloutMessage = $this->callouts['login_incomplete_profile_singleCenter'];
                $routeName = InstitutionSignupStepStatus::getRouteNameByStatus($signupStepStatus);
                if($signupStepStatus > InstitutionSignupStepStatus::STEP1) {
                    $medicalCenter =  $this->medicalCenterService->getFirstByInstitutionId($session->get('institutionId'));
                    if($medicalCenter) {
                        $routeParams = array('imcId' => $medicalCenter->getId());                        
                    }
                }
 
            } else {
                $calloutMessage = $this->callouts['login_incomplete_profile_multipleCenter'];
                $routeName = InstitutionSignupStepStatus::getMultipleCenterRouteNameByStatus($signupStepStatus);
            }            
        }

        $calloutMessage['highlight'] = str_replace('{FIRST_NAME}', $session->get('userFirstName'), $calloutMessage['highlight']);        
        $session->getFlashBag()->add('callout_message', $calloutMessage);
        $responseUrl = $this->router->generate($routeName, $routeParams);

        return new RedirectResponse($responseUrl);

    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $url = $this->router->generate('institution_login');

        return new RedirectResponse($url);
    }
}