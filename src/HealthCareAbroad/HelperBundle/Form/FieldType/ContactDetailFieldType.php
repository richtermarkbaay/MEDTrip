<?php

namespace HealthCareAbroad\HelperBundle\Form\FieldType;

use HealthCareAbroad\HelperBundle\Services\ContactDetailService;

use HealthCareAbroad\HelperBundle\Form\DataTransformer\ContactDetailDataTransformer;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ContactDetailFieldType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(new ContactDetailDataTransformer());
        $builder->add('country_code');
        $builder->add('abbr');
        $builder->add('number');
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
                        array('data_class' => 'HealthCareAbroad\HelperBundle\Entity\ContactDetail')
                        );
    }
    
    public function getName()
    {
        return 'contact_detail';
    }
    
}