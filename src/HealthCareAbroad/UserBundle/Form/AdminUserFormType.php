<?php
/**
 * 
 * @author Allejo Chris G. Velarde
 *
 */
namespace HealthCareAbroad\UserBundle\Form;

use HealthCareAbroad\UserBundle\Entity\AdminUserType;

use HealthCareAbroad\HelperBundle\Validator\Constraints\ValidAccountEmail;

use HealthCareAbroad\HelperBundle\Validator\Constraints\EqualFieldValue;

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
        $builder->add('adminUserType', 'userType_list', array('label' =>'User Type',));
        $builder->add('email','email', array('constraints' => array(
                new NotBlank(), new ValidAccountEmail(array('field' => 'email', 'currentAccountEmail' => $options['data']->getEmail())))
            ));
        $builder->add( 'password', 'password', array(
            'label' => 'Password',
            'constraints' => array(new NotBlank())
        ));
        $builder->add('confirm_password', 'password', array(
            'label' => 'Confirm Password',
            'virtual' => true,
            'constraints' => array(new EqualFieldValue(array('field' => 'password', 'message' => 'Passwords do not match')))
        ));
    }
    
    public function getName()
    {
        return 'adminUserForm';
    }
}