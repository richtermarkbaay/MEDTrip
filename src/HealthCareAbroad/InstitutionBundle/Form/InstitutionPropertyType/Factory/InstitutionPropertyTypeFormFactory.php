<?php

namespace HealthCareAbroad\InstitutionBundle\Form\InstitutionPropertyType\Factory;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter;

use HealthCareAbroad\InstitutionBundle\Form\InstitutionPropertyType\InstitutionPropertyCustomFormType;

use Symfony\Component\Form\FormFactoryInterface;

use HealthCareAbroad\InstitutionBundle\Entity\Institution;

use HealthCareAbroad\InstitutionBundle\Services\InstitutionPropertyService;
/**
 * 
 * @author Allejo Chris G. Velarde
 *
 */
class InstitutionPropertyTypeFormFactory
{
    private $formFactory;
    
    /**
     * @var InstitutionPropertyService
     */
    private $institutionPropertyService;
    
    public function __construct(FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
    }
    
    public function setInstitutionPropertyService(InstitutionPropertyService $propertyService)
    {
        $this->institutionPropertyService = $propertyService;
    }
    
    /**
     * 
     * @param Institution $institution
     * @param unknown_type $propertyTypeName
     * @return InstitutionPropertyCustomFormType
     */
    public function buildFormByInstitutionPropertyTypeName(Institution $institution, $propertyTypeName)
    {
        $institutionProperty = $this->institutionPropertyService->createInstitutionPropertyByName($propertyTypeName, $institution);
        
        $formType = new InstitutionPropertyCustomFormType();
        
        return $this->formFactory->create($formType, $institutionProperty);
    }
}