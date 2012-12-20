<?php

namespace HealthCareAbroad\InstitutionBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\AbstractType;

class InstitutionAffiliationsSelectorFormType extends AbstractType
{
    const NAME = 'institution_affiliations_selector';
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('awards_selector', 'text', array('label' => 'Search and Add your Awards'));
        $builder->add('certifications_selector', 'text', array('label' => 'Search and Add your Certifications'));
        $builder->add('affiliations_selector', 'text', array('label' => 'Search and Add your Affiliations'));
    }
    
    public function getName()
    {
        return self::NAME;
    }
}