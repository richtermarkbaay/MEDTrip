<?php
namespace HealthCareAbroad\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormViewInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
class UserAccountDetailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('firstName','text', array('constraints' => new NotBlank()));
        $builder->add('middleName','text', array('constraints' => new NotBlank()));
        $builder->add('lastName','text', array('constraints' => new NotBlank()));
        $builder->add('email','text', array('constraints' => new NotBlank() ));
    
    }
    
    public function getName()
    {
        return 'userAccountDetail';
    }
    
}