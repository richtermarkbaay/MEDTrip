<?php
namespace HealthCareAbroad\UserBundle\Handler;

use HealthCareAbroad\InstitutionBundle\Services\SignUpService;

use HealthCareAbroad\InstitutionBundle\Services\InstitutionMedicalCenterService;

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
    protected $institutionSignUpService;

    public function __construct(Router $router, InstitutionMedicalCenterService $medicalCenterService, $callouts)
    {
        $this->router = $router;
        $this->callouts = $callouts;
        $this->medicalCenterService = $medicalCenterService;
    }
    
    public function setInstitutionSignUpService(SignUpService $v=null)
    {
        $this->institutionSignUpService = $v;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    { 
//         $routeParams = array();
//         $routeName = 'institution_homepage';

// //         $session = $request->getSession();
// //         $calloutMessage = $this->callouts['login_complete_profile'];
// //         $calloutMessage['highlight'] = str_replace('{FIRST_NAME}', $session->get('userFirstName'), $calloutMessage['highlight']);        
// //         $session->getFlashBag()->add('callout_message', $calloutMessage);
//         $responseUrl = $this->router->generate($routeName, $routeParams);

//         return new RedirectResponse($responseUrl);

    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $url = $this->router->generate('institution_login');

        return new RedirectResponse($url);
    }
}