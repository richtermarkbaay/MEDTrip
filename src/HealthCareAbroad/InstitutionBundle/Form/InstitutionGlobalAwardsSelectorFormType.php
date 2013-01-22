<?php

namespace HealthCareAbroad\InstitutionBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\AbstractType;

class InstitutionGlobalAwardsSelectorFormType extends AbstractType
{
    const NAME = 'institution_global_awards_selector';
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('awards_selector', 'text', array('label' => 'Search and Add your Awards', 'attr' => array('class' => 'award_selector')));
        $builder->add('certifications_selector', 'text', array('label' => 'Search and Add your Certifications', 'attr' => array('class' => 'certificate_selector')));
        $builder->add('affiliations_selector', 'text', array('label' => 'Search and Add your Affiliations', 'attr' => array('class' => 'affiliation_selector')));
        $builder->add('accreditations_selector', 'text', array('label' => 'Search and Add your Accreditations', 'attr' => array('class' => 'accreditations_selector')));
    }
    
    public function getName()
    {
        return self::NAME;
    }
}