<?php

namespace HealthCareAbroad\InstitutionBundle\Form;

use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormViewInterface;

use HealthCareAbroad\InstitutionBundle\Form\ListType\InstitutionDoctorListType;
use HealthCareAbroad\InstitutionBundle\Form\Transformer\DoctorTransformer;

class InstitutionDoctorSearchFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('firstName', 'doctor_list', array('label' => 'Search Doctor\'s Name'));
    }
    
    public function getName()
    {
        return 'institutionDoctorSearch';
    }
}