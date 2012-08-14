<?php

namespace HealthCareAbroad\PageBundle\Form;

use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormViewInterface;

class InquireType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
    {
    	$builder
    		->add('firstName', 'text', array('constraints' => new NotBlank()))
    		->add('lastName', 'text', array('constraints' => new NotBlank()))
    		->add('email','email', array('constraints' => array(new Email(), new NotBlank())))
    		->add('subject', 'choice')
    		->add('message', 'textarea', array('constraints' => new NotBlank()))
    		;
    }
    
    public function getName()
    {
        return 'inquire';
    }
}
