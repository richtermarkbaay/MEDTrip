<?php
/**
 * Twig extension for Institution Properties
 * @author Chaztine Blance
 */
namespace HealthCareAbroad\HelperBundle\Twig;

use HealthCareAbroad\HelperBundle\Entity\GlobalAward;

use HealthCareAbroad\HelperBundle\Entity\GlobalAwardTypes;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionPropertyType;

use HealthCareAbroad\InstitutionBundle\Entity\Institution;

use HealthCareAbroad\InstitutionBundle\Services\InstitutionPropertyService;

class InstitutionPropertiesTwigExtension extends \Twig_Extension
{

    static $anciliaryServices = array();

    static $globalAwards = array();
    
    
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
            'get_globalAward_type' => new \Twig_Function_Method($this, 'getGlobalAwardType')
        );
    }

    public function getselected_AnciliaryServices(Institution $institution)
    {
        if(empty(static::$anciliaryServices)) {
            $ancillaryServices = $this->service->getInstitutionByPropertyType($institution, InstitutionPropertyType::TYPE_ANCILLIARY_SERVICE);

            foreach ($ancillaryServices as $each) {
                static::$anciliaryServices[$each->getValue()] = $each->getId();
            }
        }

        return static::$anciliaryServices; 
    }

    /**
     * TODO: Not tested and not being use.
     * @param Institution $institution
     * @return multitype:
     */
    public function getselected_GlobalAwards(Institution $institution){
        
        if(empty(static::$globalAwards)) {
            $awards = $this->service->getGlobalAwardPropertiesByInstitution($institution);
            foreach ($awards as $each) {
                foreach ($each as $data) {
                    static::$globalAwards[$data->getValue()] = $data->getExtraValue();
                }
            }            
        }

        return static::$globalAwards;
    }
    
    public function getGlobalAwardType($type)
    {
        return GlobalAwardTypes::getTypeValue($type);
    }
    
     public function getName()
     {
         return 'institutionProperties';
     }
    
}