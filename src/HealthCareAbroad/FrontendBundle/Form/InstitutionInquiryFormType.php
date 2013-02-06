<?php

namespace HealthCareAbroad\FrontendBundle\Form;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionInquiry;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormViewInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class InstitutionInquiryFormType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
    {
    	$builder
    	    ->add('inquirerName', 'text', array('constraints' => array(new NotBlank()), 'label' => 'Your Name' ))
    		->add('inquirerEmail','email', array('constraints' => array(new Email(), new NotBlank()), 'label' => 'Your Email Address'))
    		->add('message', 'textarea', array('constraints' => new NotBlank(), 'label' => 'Enter Your Message'))
    		;
    }
    
    public function getName()
    {
        return 'institutionInquiry';
    }
}

