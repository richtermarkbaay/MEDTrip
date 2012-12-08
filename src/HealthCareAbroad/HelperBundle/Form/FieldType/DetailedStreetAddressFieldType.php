<?php

namespace HealthCareAbroad\HelperBundle\Form\FieldType;

use HealthCareAbroad\HelperBundle\Form\DataTransformer\DetailedStreetAddressDataTransformer;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\AbstractType;

class DetailedStreetAddressFieldType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(new DetailedStreetAddressDataTransformer());
    }
    
    public function getName()
    {
        return 'detailed_street_address';
    }
    
    public function getParent()
    {
        return 'text';
    }
}