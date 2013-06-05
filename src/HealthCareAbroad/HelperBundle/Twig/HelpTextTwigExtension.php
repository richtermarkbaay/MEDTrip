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
            'add_slashes' => new \Twig_Function_Method($this, 'addSlashes'),
            'strpos' => new \Twig_Function_Method($this, 'strpos'),
            'hasSubstr' => new \Twig_Function_Method($this, 'hasSubstr'),
            'lcfirst' => new \Twig_Function_Method($this, 'lcfirst'),
            'substr' => new \Twig_Function_Method($this, 'substr'),
        );
     }
     
     public function getHelpTextByRoute($routeName)
     {
         $value =  $this->repository->findOneBy(array('route' => $routeName));
         if($value){
             $returnValue = $value->getDetails();
         }else{
             $returnValue = '';
         }
         
         return $returnValue;
     }
     
     public function addSlashes($string)
     {
         return addslashes($string);
     }
     
     public function strpos($string, $findme)
     {
         return strpos($string, $findme);
     }

     public function substr($string, $start, $length = null)
     {
         if(!$length) {
             $length = strlen($string);
         }
         
        return substr($string, $start, $length);
     }

     public function hasSubstr($string, $findme)
     {
         $pos = strpos($string, $findme);

         return $pos !== false;
     }
     
     public function lcfirst($string)
     {
         return \lcfirst($string);
     }

     public function getName()
     {
         return 'helptext';
     }
    
}