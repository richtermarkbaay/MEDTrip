<?php

namespace HealthCareAbroad\MedicalProcedureBundle\Form\ListType;

use Symfony\Component\Form\AbstractType;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class MedicalProcedureListType extends AbstractType
{
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'virtual' => true,
            'empty_value' => 'Please select one',
            'property' => 'name',
            'class' => 'HealthCareAbroad\MedicalProcedureBundle\Entity\MedicalProcedure',
        ));
    }
    
    public function getName()
    {
        return 'medicalProcedureList';
    }
    
    public function getParent()
    {
        return 'entity';
    }
}