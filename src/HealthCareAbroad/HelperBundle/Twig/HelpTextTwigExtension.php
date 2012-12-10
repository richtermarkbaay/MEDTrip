<?php
/**
 * Twig extension for helper text by route
 * @author Chaztine Blance
 */
namespace HealthCareAbroad\HelperBundle\Twig;

use HealthCareAbroad\HelperBundle\Entity\RouteType;

use HealthCareAbroad\HelperBundle\Entity\HelperText;

class HelpTextTwigExtension extends \Twig_Extension
{
    protected $doctrine;
    
    public function __construct(\Doctrine\Bundle\DoctrineBundle\Registry $doctrine )
    {
        $this->doctrine = $doctrine;
        $this->repository = $this->doctrine->getRepository('HelperBundle:HelperText');
    }
    
    public function getFunctions()
    {
        return array(
                        'get_help_text_by_route' => new \Twig_Function_Method($this, 'getHelpTextByRoute'),
        );
     }
     
     public function getHelpTextByRoute($routeName)
     {
         $value =  $this->repository->findOneBy(array('route' => $routeName));
         $returnValue = $value->getDetails();
         
         return $returnValue;
     }
     
     public function getName()
     {
         return 'helptext';
     }
    
}