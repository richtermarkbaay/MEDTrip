<?php
/**
 * Twig extension for Institution Type Label
 * @author Chaztine Blance
 */
namespace HealthCareAbroad\HelperBundle\Twig;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionTypes;

use HealthCareAbroad\HelperBundle\Entity\RouteType;

use HealthCareAbroad\HelperBundle\Entity\HelperText;

class InstitutionTypeTwigExtension extends \Twig_Extension
{
    public function getFunctions()
    {
        return array(
                'get_institution_type' => new \Twig_Function_Method($this, 'getInstitutionType'),
        );
     }
     
     public function getInstitutionType($types)
     {
         $institutionType = 'Single';
         if ($types == 1) {
             $institutionType = 'Multiple';
         }
         
         return $institutionType;
     }
     
     public function getName()
     {
         return 'institutionTypeText';
     }
}