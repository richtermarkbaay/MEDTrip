<?php
/**
 * Twig extension for Institution Properties
 * @author Chaztine Blance
 */
namespace HealthCareAbroad\HelperBundle\Twig;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionPropertyType;

use HealthCareAbroad\InstitutionBundle\Entity\Institution;

use HealthCareAbroad\InstitutionBundle\Services\InstitutionPropertyService;

class InstitutionPropertiesTwigExtension extends \Twig_Extension
{
    
    /**
     * @var \Twig_Environment
     */
    private $twig;
    private $service;
    
    public function setTwig($v)
    {
        $this->twig = $v;
    }
    public function __construct(InstitutionPropertyService $service)
    {
        $this->service = $service;
    }
    
    public function getFunctions()
    {
        return array(
            'get_selected_AnciliaryServices' => new \Twig_Function_Method($this, 'getselected_AnciliaryServices'),
            'get_selected_GlobalAwards' => new \Twig_Function_Method($this, 'getselected_GlobalAwards'),
        );
    }
    
    public function getselected_AnciliaryServices(Institution $institution)
    {
        $ancillaryServicesData = array(
                        'currentAncillaryData' => array(),
                        'data' =>array(),
        );
        
        foreach ($this->service->getInstitutionByPropertyType($institution, InstitutionPropertyType::TYPE_ANCILLIARY_SERVICE) as $_selectedService) {
            $ancillaryServicesData['currentAncillaryData'][] = array(
                            'id' => $_selectedService->getId(),
                            'value' => $_selectedService->getValue(),
            );
            $ancillaryServicesData['data'][] = $_selectedService->getValue();
        }
        return $ancillaryServicesData;
    }
    public function getselected_GlobalAwards(Institution $institution){
        
        $currentGlobalAwards = array(
                        'currentAwardsData' => array(),
                        'data' =>array(),
        );
        foreach ($this->service->getGlobalAwardPropertiesByInstitution($institution) as $_selected) {
            foreach ($_selected as $data) {
                $currentGlobalAwards['currentAwardsData'][] = array(
                                'id' => $data->getId(),
                                'value' => $data->getValue(),
                );
                $currentGlobalAwards['data'][] = $data->getValue();
            }
        }
        return $currentGlobalAwards;
    }
    
     public function getName()
     {
         return 'institutionProperties';
     }
    
}