<?php

namespace HealthCareAbroad\InstitutionBundle\Form\InstitutionMedicalCenterPropertyType;

use HealthCareAbroad\InstitutionBundle\Form\ListType\InstitutionOfferedServiceListType;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenterProperty;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionPropertyType;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\AbstractType;

class InstitutionMedicalCenterPropertyCustomFormType extends AbstractType
{
    public function getName()
    {
        return 'institution_medical_center_property_type_custom_form';
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $imcProperty = $options['data'];
        if (!$imcProperty instanceof InstitutionMedicalCenterProperty) {
            throw new \Exception(__CLASS__." form expects an instance of InstitutionMedicalCenterProperty");
        }
        
        $institutionPropertyType = $imcProperty->getInstitutionPropertyType();
        $formOptions = \json_decode($institutionPropertyType->getFormConfiguration(), true);
        if (\is_array($formOptions) && \array_key_exists('type', $formOptions)) {
            $fieldType = new $formOptions['type'];
            if (!$fieldType instanceof AbstractType) {
                throw new \Exception (\sprintf('Form option type must be a class of type Abstract type, %s given.', \get_class($fieldType))); 
            }
            
            unset($formOptions['type']);
        }
        else {
            $fieldType = new InstitutionMedicalCenterPropertyValueCustomFieldType($imcProperty);
        }
        if (\is_null($formOptions) || !\is_array($formOptions)) {
            $formOptions = array();
        }
        $formOptions['label'] = $institutionPropertyType->getLabel();
        
        
        $builder->add('value', $fieldType, $formOptions);
    }
}