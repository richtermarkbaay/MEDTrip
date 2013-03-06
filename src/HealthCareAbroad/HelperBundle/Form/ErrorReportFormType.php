<?php

namespace HealthCareAbroad\HelperBundle\Form;

use HealthCareAbroad\AdminBundle\Entity\ErrorReport;
use Symfony\Component\Validator\Constraints\NotBlank;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ErrorReportFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('details','textarea', array('label' => 'What seems to be the problem?'));          
        $builder->add('captcha', 'captcha', array('label' => 'Please type the code'));
    }
    
    // How does it work?
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
                        'data_class' => 'HealthCareAbroad\AdminBundle\Entity\ErrorReport',
        ));
    }
    
    public function getName()
    {
        return 'ExceptionForm';
    }
}

