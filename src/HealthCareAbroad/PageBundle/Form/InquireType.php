<?php

namespace HealthCareAbroad\PageBundle\Form;

use HealthCareAbroad\PageBundle\Form\ListType\InquireAboutListType;

use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormViewInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class InquireType extends AbstractType
{
	private $container;
	
	public function setContainer(ContainerInterface $container = null) {
		$this->container = $container;
	}
	public function buildForm(FormBuilderInterface $builder, array $options)
    {
    	$builder
    		->add('firstName', 'text', array('constraints' => new NotBlank()))
    		->add('lastName', 'text', array('constraints' => new NotBlank()))
    		->add('email','email', array('constraints' => array(new Email(), new NotBlank())))
    		->add('inquire_about', 'inquire_about_list')
    		->add('message', 'textarea', array('constraints' => new NotBlank()))
    		;
    }
    
    public function getName()
    {
        return 'inquire';
    }
}
