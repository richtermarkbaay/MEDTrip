<?php

namespace HealthCareAbroad\TermBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\AbstractType;

class TermFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', 'text', array('error_bubbling' => true, 'attr' => array('placeholder' => "New term/tag")));
    }
    
    public function getName()
    {
        return 'term_form';
    }
}