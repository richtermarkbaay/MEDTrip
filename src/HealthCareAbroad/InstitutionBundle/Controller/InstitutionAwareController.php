<?php
/**
 * Base class for controllers that needs an instance of an Institution
 * 
 * @author Allejo Chris G. Velarde
 *
 */
namespace HealthCareAbroad\InstitutionBundle\Controller;

use HealthCareAbroad\InstitutionBundle\Entity\Institution;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

abstract class InstitutionAwareController extends Controller
{
    /**
     * @var Institution
     */
    protected $institution;
    
    public function setInstitution(Institution $institution)
    {
        $this->institution = $institution;
    }
    
    public function throwInvalidInstitutionException()
    {
        throw $this->createNotFoundException("Invalid institution");
    }
}