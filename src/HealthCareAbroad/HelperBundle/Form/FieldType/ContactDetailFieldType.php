<?php

namespace HealthCareAbroad\HelperBundle\Form\FieldType;

use HealthCareAbroad\HelperBundle\Services\ContactDetailService;

use HealthCareAbroad\HelperBundle\Form\DataTransformer\ContactDetailDataTransformer;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\AbstractType;

class ContactDetailFieldType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(new ContactDetailDataTransformer());
    }
    
    public function getName()
    {
        return 'contact_detail';
    }
    
    public function getParent()
    {
        return 'entity';
    }
}