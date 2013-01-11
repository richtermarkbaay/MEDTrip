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
        foreach ($treatments as $_treatment) {
            if (!$_treatment instanceof Treatment) {
                continue;
            }
            
            foreach ($_treatment->getSubSpecializations() as $_subSpecialization) {
                if (!\array_key_exists($_subSpecialization->getId(), $grouped)) {
                    $grouped[$_subSpecialization->getId()] = array('treatments' => array(), 'subSpecialization' => $_subSpecialization);
                }
                $grouped[$_subSpecialization->getId()]['treatments'][] = $_treatment;
            }
        }
        
        return $grouped;
    }
}