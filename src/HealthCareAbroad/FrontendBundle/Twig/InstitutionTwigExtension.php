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
            'get_frontend_url' => new \Twig_Function_Method($this, 'getInstitutionFrontendUrl'),
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
        $uri = $this->router->generate(
            $this->institutionService->isSingleCenter($institution)
                ? 'frontend_single_center_institution_profile'
                : 'frontend_multiple_center_institution_profile', 
            array('institutionSlug' => $institution->getSlug()), 
            true
        );
        
        return $uri;
    }
    
    public function getInstitutionMedicalCenterFrontendUrl(InstitutionMedicalCenter $institutionMedicalCenter)
    {
        return $this->router->generate('page_institutionMedicaCenter_profile', array(
            'institutionSlug' => $institutionMedicalCenter->getInstitution()->getSlug(),
            'imcSlug' => $institutionMedicalCenter->getSlug()
        ));
    }
    
    public function getName()
    {
        return 'frontend_institution_twig_extension';
    }
}