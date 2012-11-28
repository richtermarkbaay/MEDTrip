<?php

namespace HealthCareAbroad\InstitutionBundle\Form\InstitutionPropertyType;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionProperty;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionPropertyType;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\AbstractType;

class InstitutionPropertyCustomFormType extends AbstractType
{
    public function getName()
    {
        return 'institution_property_type_custom_form';
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $institutionProperty = $options['data'];
        if (!$institutionProperty instanceof InstitutionProperty) {
            throw new \Exception(__CLASS__." form expects an instance of InstitutionProperty");
        }
        
        $institutionPropertyType = $institutionProperty->getInstitutionPropertyType();
        
        $fieldType = new InstitutionPropertyValueCustomFieldType($institutionProperty);
        
        $formOptions = \json_decode($institutionPropertyType->getFormConfiguration(), true);
        if (\is_null($formOptions) || !\is_array($formOptions)) {
            $formOptions = array();
        }
        $formOptions['label'] = $institutionPropertyType->getLabel();
        
        $builder->add('value', $fieldType, $formOptions);
    }
}