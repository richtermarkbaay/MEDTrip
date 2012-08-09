<?php
/**
 * 
 * @author Allejo Chris G. Velarde
 *
 */
namespace HealthCareAbroad\UserBundle\Form;

use Symfony\Component\Validator\Constraints\NotBlank;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\FormViewInterface;

use Symfony\Component\Form\AbstractType;

class AdminUserTypeFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name','text', array('label' => 'User type name:','constraints' => new NotBlank()));
        
    
    }
    
    public function getName()
    {
        return 'adminUserTypeForm';
    }
}