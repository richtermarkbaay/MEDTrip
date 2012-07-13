<?php

namespace HealthCareAbroad\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormViewInterface;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    	$builder->add('email','email');
    	$builder->add('password','password');
   

    }

    public function getName()
    {
        return 'userBundle_login';
    }
}