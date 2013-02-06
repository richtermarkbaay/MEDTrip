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
    	    ->add('inquirer_name', 'text', array('label' => 'Your Name' ))
    		->add('inquirer_email','email', array('label' => 'Your Email Address'))
    		->add('message', 'textarea', array('label' => 'Enter Your Message'))
    		;
    }
    
    public function getName()
    {
        return 'institutionInquiry';
    }
}

