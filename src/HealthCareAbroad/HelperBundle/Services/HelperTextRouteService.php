<?php
/**
 * Service class for Helper Text Route
 * 
 * @author Chaztine Blance
 */
namespace HealthCareAbroad\HelperBundle\Services;

use HealthCareAbroad\HelperBundle\Entity\RouteType;

use HealthCareAbroad\HelperBundle\Entity\HelperText;

class HelperTextRouteService
{
	
    protected $doctrine;
    
    protected $helperText;
    public function __construct(\Doctrine\Bundle\DoctrineBundle\Registry $doctrine )
    {
    	$this->doctrine = $doctrine;
    	$this->repository = $this->doctrine->getRepository('HelperBundle:HelperText');
    }
    
    public function getHelperTextByRoute($route)
    {
        $value =  $this->repository->findOneBy(array('route' => $route));
     
        $returnValue = $value->getDetails();
        
        return $returnValue;
    }
    
}