<?php
namespace HealthCareAbroad\HelperBundle\Form;

use Symfony\Component\Validator\Constraints\NotBlank;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\AbstractType;

class BreadcrumbFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('route', 'text', array('constraints' => array(new NotBlank())))
            ->add('label', 'text', array('constraints' => array(new NotBlank())));
        
    }
    
    public function getName()
    {
        return 'breadcrumbForm';
    }
} 