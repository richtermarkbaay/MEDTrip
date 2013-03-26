<?php

namespace HealthCareAbroad\HelperBundle\Form\FieldType;

use HealthCareAbroad\HelperBundle\Form\DataTransformer\ContactNumberWithWidgetDataTransformer;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\AbstractType;

class ContactNumberWithFlagFieldType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(new ContactNumberWithWidgetDataTransformer());
    }
    
    public function getName()
    {
        return 'contact_number_with_flag';
    }
    
    public function getParent()
    {
        return 'text';
    }
}