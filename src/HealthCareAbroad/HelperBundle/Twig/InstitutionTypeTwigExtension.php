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
         $returnValue = InstitutionTypes::getLabelList();

         return $returnValue[$types];
     }
     
     public function getName()
     {
         return 'institutionTypeText';
     }
}