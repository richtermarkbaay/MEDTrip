<?php

namespace HealthCareAbroad\InstitutionBundle\Form\InstitutionMedicalCenterPropertyType\Factory;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter;

use HealthCareAbroad\InstitutionBundle\Form\InstitutionMedicalCenterPropertyType\InstitutionMedicalCenterPropertyCustomFormType;

use Symfony\Component\Form\FormFactoryInterface;

use HealthCareAbroad\InstitutionBundle\Entity\Institution;

use HealthCareAbroad\InstitutionBundle\Services\InstitutionMedicalCenterPropertyService;
/**
 * 
 * @author Allejo Chris G. Velarde
 *
 */
class InstitutionMedicalCenterPropertyTypeFormFactory
{
    private $formFactory;
    
    /**
     * @var InstitutionMedicalCenterPropertyService
     */
    private $imcPropertyService;
    
    public function __construct(FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
    }
    
    public function setInstitutionMedicalCenterPropertyService(InstitutionMedicalCenterPropertyService $propertyService)
    {
        $this->imcPropertyService = $propertyService;
    }
    
    /**
     *
     * @param Institution $institution
     * @param InstitutionMedicalCenter $institutionMedicalCenter
     * @param unknown_type $propertyTypeName
     * @return InstitutionMedicalCenterPropertyCustomFormType
     */
    public function buildFormByInstitutionMedicalCenterPropertyTypeName(Institution $institution, InstitutionMedicalCenter $center, $propertyTypeName)
    {
        $imcProperty = $this->imcPropertyService->createInstitutionMedicalCenterPropertyByName($propertyTypeName, $institution, $center);
    
        $formType = new InstitutionMedicalCenterPropertyCustomFormType();
    
        return $this->formFactory->create($formType, $imcProperty);
    }
    
}