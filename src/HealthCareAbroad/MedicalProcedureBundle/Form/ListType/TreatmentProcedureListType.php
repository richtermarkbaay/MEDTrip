<?php

namespace HealthCareAbroad\MedicalProcedureBundle\Form\ListType;

use Symfony\Component\Form\AbstractType;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TreatmentProcedureListType extends AbstractType
{
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'virtual' => false,
            'empty_value' => 'Please select one',
            'property' => 'name',
            'class' => 'HealthCareAbroad\MedicalProcedureBundle\Entity\TreatmentProcedure',
        ));
    }
    
    public function getName()
    {
        return 'treatmentProcedureList';
    }
    
    public function getParent()
    {
        return 'entity';
    }
}