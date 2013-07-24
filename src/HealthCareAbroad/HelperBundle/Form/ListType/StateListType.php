<?php

namespace HealthCareAbroad\HelperBundle\Form\ListType;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Symfony\Component\Form\AbstractType;

class StateListType extends AbstractType
{
    public function getName()
    {
        return 'state_list';
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'empty_value' => 'Select State',
            'choices' => array()
        ));
    }
    
    public function getParent()
    {
        return 'choice';
    }
    
}