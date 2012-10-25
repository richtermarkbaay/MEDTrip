<?php

namespace HealthCareAbroad\TreatmentBundle\Form\ListType;

use Symfony\Component\Form\AbstractType;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TreatmentListType extends AbstractType
{
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'virtual' => false,
            'empty_value' => 'Please select one',
            'property' => 'name',
            'class' => 'HealthCareAbroad\TreatmentBundle\Entity\Treatment',
        ));
    }
    
    public function getName()
    {
        return 'treatment_list';
    }
    
    public function getParent()
    {
        return 'entity';
    }
}