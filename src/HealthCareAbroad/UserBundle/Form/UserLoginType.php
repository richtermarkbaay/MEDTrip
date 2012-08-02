<?php

namespace HealthCareAbroad\UserBundle\Form;

use Symfony\Component\Validator\Constraints\NotBlank;

use Symfony\Component\Validator\Constraints\Email;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormViewInterface;

class UserLoginType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    	$builder->add('email','email', array('constraints' => new Email()));
    	$builder->add('password','password', array('constraints' => new NotBlank()));

    }

    public function getName()
    {
        return 'userLogin';
    }
}