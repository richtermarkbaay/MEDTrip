<?php

namespace HealthCareAbroad\HelperBundle\Form\FieldType;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Symfony\Component\Form\AbstractType;

class SimpleContactDetailFieldType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options=array())
    {
        $builder->add('country_code', 'country_code_list');
        $builder->add('area_code', 'text', array('attr' => array('placeholder' => 'Area Code')));
        $builder->add('number', 'text', array('attr' => array('placeholder' => 'Phone Number')));
    }
    
    public function getName()
    {
        return 'simple_contact_detail';
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'HealthCareAbroad\HelperBundle\Entity\ContactDetail'
        ));
    }
}