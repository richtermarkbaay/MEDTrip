<?php

namespace HealthCareAbroad\InstitutionBundle\Form\InstitutionPropertyType;

use HealthCareAbroad\InstitutionBundle\Form\ListType\InstitutionOfferedServiceListType;

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
        $formOptions = \json_decode($institutionPropertyType->getFormConfiguration(), true);
        if (\is_array($formOptions) && \array_key_exists('type', $formOptions)) {
            $fieldType = new $formOptions['type'];
            
            if (!$fieldType instanceof AbstractType) {
                throw new \Exception (\sprintf('Form option type must be a class of type Abstract type, %s given.', \get_class($fieldType))); 
            }
            
            unset($formOptions['type']);
        }
        else {
            $fieldType = new InstitutionPropertyValueCustomFieldType($institutionProperty);
        }
        if (\is_null($formOptions) || !\is_array($formOptions)) {
            $formOptions = array();
        }
        $formOptions['label'] = $institutionPropertyType->getLabel();
        
        
        $builder->add('value', $fieldType, $formOptions);
    }
}