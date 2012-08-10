<?php
/**
 * 
 * @author Allejo Chris G. Velarde
 *
 */
namespace HealthCareAbroad\UserBundle\Form;

use Symfony\Component\Validator\Constraints\Email;

use Symfony\Component\Form\AbstractType;

use Symfony\Component\Validator\Constraints\NotBlank;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\FormViewInterface;

class AdminUserFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('firstName','text', array('constraints' => new NotBlank()));
        $builder->add('middleName','text', array('constraints' => new NotBlank()));
        $builder->add('lastName','text', array('constraints' => new NotBlank()));
        $builder->add('email','email', array('constraints' => array(
                new NotBlank(), new Email())
            ));
    
    }
    
    public function getName()
    {
        return 'adminUserForm';
    }
}