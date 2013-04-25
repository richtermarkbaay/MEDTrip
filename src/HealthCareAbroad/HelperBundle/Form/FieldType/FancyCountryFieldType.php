<?php

namespace HealthCareAbroad\HelperBundle\Form\FieldType;

use HealthCareAbroad\HelperBundle\Form\DataTransformer\CountryTransformer;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\AbstractType;

class FancyCountryFieldType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(new CountryTransformer($service));
    }
    
    public function getName()
    {
        return 'fancy_country';
    }
    
    public function getParent()
    {
        return 'text';
    }
}