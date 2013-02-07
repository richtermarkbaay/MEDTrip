<?php

namespace HealthCareAbroad\HelperBundle\Twig;

use HealthCareAbroad\TreatmentBundle\Entity\Treatment;

class TreatmentsTwigExtension extends \Twig_Extension
{
    public function getFunctions()
    {
        return array(
            'group_treatments_by_subspecialization' => new \Twig_Function_Method($this, 'group_treatments_by_subspecialization')
        );
    }
    
    public function getName()
    {
        return 'treatments_extension';
    } 
    
    
    public function group_treatments_by_subspecialization($treatments)
    {
        $grouped = array();
        $noSubspecializations = array('treatments' => array(), 'subSpecialization' => null);
        foreach ($treatments as $_treatment) {
            if (!$_treatment instanceof Treatment) {
                continue;
            }
            
            $subSpecializations = $_treatment->getSubSpecializations();
            if (count($subSpecializations)) {
                foreach ($subSpecializations as $_subSpecialization) {
                    $_key = $_subSpecialization->getName();
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