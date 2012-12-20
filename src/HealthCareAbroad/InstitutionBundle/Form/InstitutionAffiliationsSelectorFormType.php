<?php

namespace HealthCareAbroad\InstitutionBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\AbstractType;

class InstitutionGlobalAwardsSelectorFormType extends AbstractType
{
    const NAME = 'institution_global_awards_selector';
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('awards_selector', 'text', array('label' => 'Search and Add your Awards'));
        $builder->add('certifications_selector', 'text', array('label' => 'Search and Add your Certifications'));
        $builder->add('global_awards_selector', 'text', array('label' => 'Search and Add your GlobalAwards'));
    }
    
    public function getName()
    {
        return self::NAME;
    }
}