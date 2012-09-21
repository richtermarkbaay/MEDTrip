<?php

namespace HealthCareAbroad\FrontendBundle\Form;

use HealthCareAbroad\FrontendBundle\Form\ListType\InquirySubjectListType;

use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormViewInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class InquiryType extends AbstractType
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
    		->add('inquiry_subject', 'inquiry_subject_list')
    		->add('message', 'textarea', array('constraints' => new NotBlank()))
    		;
    }
    
    public function getName()
    {
        return 'inquire';
    }
}
