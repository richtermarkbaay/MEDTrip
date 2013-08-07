<?php

namespace HealthCareAbroad\HelperBundle\Twig;

use HealthCareAbroad\TreatmentBundle\Entity\SubSpecialization;

use HealthCareAbroad\TreatmentBundle\Entity\Treatment;

class TreatmentsTwigExtension extends \Twig_Extension
{
    public function getFunctions()
    {
        return array(
            'group_treatments_by_subspecialization' => new \Twig_Function_Method($this, 'groupTreatmentsBySubspecialization')
        );
    }
    
    public function getName()
    {
        return 'treatments_extension';
    } 
    
    
    public function groupTreatmentsBySubspecialization($treatments)
    {
        $grouped = array();
        $noSubspecializations = array('treatments' => array(), 'subSpecialization' => null);
        foreach ($treatments as $_treatment) {
            if ($_treatment instanceof Treatment) {
                $subSpecializations = $_treatment->getSubSpecializations();
            }
            elseif (\is_array($_treatment)){
                // hydrated with HYDRATE_ARRAY
                $subSpecializations = $_treatment['subSpecializations'];
            }
            else {
                continue;
            }
            
            if (count($subSpecializations)) {
                foreach ($subSpecializations as $_subSpecialization) {
                    if ($_subSpecialization instanceof SubSpecialization){
                        $_key = $_subSpecialization->getName();
                    }
                    else {
                        $_key = $_subSpecialization['name'];
                    }
                    
                    if (!\array_key_exists($_key, $grouped)) {
                        $grouped[$_key] = array('treatments' => array(), 'subSpecialization' => $_subSpecialization);
                    }
                    $grouped[$_key]['treatments'][] = $_treatment;
                }   
            }
            // treatment has no subspecializations
            else {
                
                $noSubspecializations['treatments'][] = $_treatment;
            }
        }
        $grouped['Other treatments'] = $noSubspecializations;
        
        return $grouped;
    }
}