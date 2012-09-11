<?php

namespace HealthCareAbroad\HelperBundle\Form;

use Symfony\Component\Form\AbstractType;

use Symfony\Component\Validator\Constraints\NotBlank;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\FormViewInterface;

class ErrorReportFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    	
        $builder->add('reporterName','text', array('constraints' => new NotBlank()));
        $builder->add('details','textarea', array('constraints' => new NotBlank()));          
    
    }
    
    public function getName()
    {
        return 'ExceptionForm';
    }
}