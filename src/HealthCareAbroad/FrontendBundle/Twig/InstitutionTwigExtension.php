<?php
namespace HealthCareAbroad\FrontendBundle\Twig;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter;

use Symfony\Bundle\FrameworkBundle\Routing\Router;

use HealthCareAbroad\InstitutionBundle\Services\InstitutionService;

use HealthCareAbroad\InstitutionBundle\Entity\Institution;

class InstitutionTwigExtension extends \Twig_Extension
{
    /**
     * @var InstitutionService
     */
    private $institutionService;
    
    /**
     * @var Router
     */
    private $router;
    
    public function getFunctions()
    {
        return array(
            'get_institution_frontend_url' => new \Twig_Function_Method($this, 'getInstitutionFrontendUrl'),
            'get_institution_medical_center_frontend_url' => new \Twig_Function_Method($this, 'getInstitutionMedicalCenterFrontendUrl')
        );
    }
    
    public function setInstitutionService(InstitutionService $s)
    {
        $this->institutionService = $s;
    }
    
    public function setRouter(Router $s)
    {
        $this->router = $s;
    }
    
    /**
     * Generate frontend uri of an institution
     * 
     * @param Institution $institution
     * @return string
     */
    public function getInstitutionFrontendUrl(Institution $institution)
    {
        $routeName = $this->institutionService->getInstitutionRouteName($institution);
        $uri = $this->router->generate($routeName, array('institutionSlug' => $institution->getSlug()), true);

        return $uri;
    }
    
    public function getInstitutionMedicalCenterFrontendUrl(InstitutionMedicalCenter $institutionMedicalCenter)
    {
        // check first if this is a single center institution
        if ($this->institutionService->isSingleCenter($institutionMedicalCenter->getInstitution())){
            $uri = $this->getInstitutionFrontendUrl($institutionMedicalCenter->getInstitution());
        }
        else {
            $uri = $this->router->generate('frontend_institutionMedicaCenter_profile', array(
                'institutionSlug' => $institutionMedicalCenter->getInstitution()->getSlug(),
                'imcSlug' => $institutionMedicalCenter->getSlug()
            ));
        }
        
        return $uri;
    }
    
    public function getName()
    {
        return 'frontend_institution_twig_extension';
    }
}