<?php
namespace HealthCareAbroad\FrontendBundle\Twig;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionTypes;

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
     * @param Mixed <Institution, array> $institution
     * @return string
     */
    public function getInstitutionFrontendUrl($institution)
    {
        if ($institution instanceof Institution){
            $slug = $institution->getSlug();
        }
        else {
            $slug = $institution['slug'];
        }
        $routeName = InstitutionService::getInstitutionRouteName($institution);
        $uri = $this->router->generate($routeName, array('institutionSlug' => $slug), true);

        return $uri;
    }
    
    public function getInstitutionMedicalCenterFrontendUrl($institutionMedicalCenter)
    {
        if ($institutionMedicalCenter instanceof InstitutionMedicalCenter){
            $type =  $institutionMedicalCenter->getInstitution()->getType();
            $institutionSlug = $institutionMedicalCenter->getInstitution()->getSlug();
            $imcSlug = $institutionMedicalCenter->getSlug();
            $institution = $institutionMedicalCenter->getInstitution();
        }
        else {
            // hydrate array
            var_dump(\array_keys($institutionMedicalCenter));
            $type = $institutionMedicalCenter['institution']['type'];
            $institutionSlug = $institutionMedicalCenter['institution']['slug'];
            $imcSlug = $institutionMedicalCenter['slug'];
            $institution = $institutionMedicalCenter['institution'];
        }
        // check first if this is a single center institution
        if (InstitutionTypes::SINGLE_CENTER == $type){
            $uri = $this->getInstitutionFrontendUrl($institution);
        }
        else {
            $uri = $this->router->generate('frontend_institutionMedicalCenter_profile', array(
                'institutionSlug' => $institutionSlug,
                'imcSlug' => $imcSlug
            ));
        }
        
        return $uri;
    }
    
    public function getName()
    {
        return 'frontend_institution_twig_extension';
    }
}