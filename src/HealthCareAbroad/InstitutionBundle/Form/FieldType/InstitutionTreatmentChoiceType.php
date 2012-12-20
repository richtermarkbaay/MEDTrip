<?php
namespace HealthCareAbroad\InstitutionBundle\Form\FieldType;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Symfony\Component\Form\AbstractType;

class InstitutionTreatmentChoiceType extends AbstractType
{
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'class' => 'HealthCareAbroad\TreatmentBundle\Entity\Treatment',
            'property' => 'name',
            'multiple' => true,
            'expanded' => true,
        ));
    }
    
    public function getParent()
    {
        return 'entity';
    }
    
    public function getName()
    {
        return 'institution_treatment_choice';
    }   
}