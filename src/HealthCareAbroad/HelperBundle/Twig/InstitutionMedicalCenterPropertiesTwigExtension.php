<?php
/**
 * Twig extension for Institution Properties
 * @author Chaztine Blance
 */
namespace HealthCareAbroad\HelperBundle\Twig;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter;

use HealthCareAbroad\InstitutionBundle\Services\InstitutionMedicalCenterPropertyService;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionPropertyType;

class InstitutionMedicalCenterPropertiesTwigExtension extends \Twig_Extension
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
    public function __construct(InstitutionMedicalCenterPropertyService $service)
    {
        $this->service = $service;
    }
    
    public function getFunctions()
    {
        return array(
            'get_selected_medicalCenterServices' => new \Twig_Function_Method($this, 'getselected_medicalCenterServices'),
            'get_selected_medicalCenterGlobalAwards' => new \Twig_Function_Method($this, 'getselected_medicalCenterGlobalAwards'),
        );
    }
    
    public function getselected_medicalCenterServices(InstitutionMedicalCenter $center)
    {
        $ancillaryServicesData = array(
                        'currentAncillaryData' => array(),
                        'data' =>array(),
        );
        foreach ($this->service->getInstitutionMedicalCenterByPropertyType($center, InstitutionPropertyType::TYPE_ANCILLIARY_SERVICE) as $_selectedService) {
            $ancillaryServicesData['currentAncillaryData'][] = array(
                            'id' => $_selectedService->getId(),
                            'value' => $_selectedService->getValue(),
            );
            $ancillaryServicesData['data'][] = $_selectedService->getValue();
        }
        
        return $ancillaryServicesData;
    }
    public function getselected_medicalCenterGlobalAwards(InstitutionMedicalCenter $center){
        
        $currentGlobalAwards = array(
                        'currentAwardsData' => array(),
                        'data' =>array(),
        );
        foreach ($this->service->getGlobalAwardPropertiesByInstitutionMedicalCenter($center) as $_selected) {
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
         return 'medicalCenterProperties';
     }
    
}