<?php

namespace HealthCareAbroad\InstitutionBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormViewInterface;

class InstitutionDoctorSearchFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('firstName', 'text', array('label' => 'Search Doctor\'s Name'))
                ->add('id', 'hidden');
    }
    
    public function getName()
    {
        return 'institutionDoctorSearch';
    }
}