<?php

namespace HealthCareAbroad\InstitutionBundle\Form;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormViewInterface;

class InstitutionDoctorFormType extends AbstractType
{
	private $container;
	
	public function setContainer(ContainerInterface $container = null) {
		$this->container = $container;
	}
	
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    	$builder
     		->add('firstName', 'text', array('label' => 'First name', 'constraints' => new NotBlank()))
     		->add('middleName', 'text', array('label' => 'Middle name'))
     		->add('lastName', 'text', array('label' => 'Last name', 'constraints' => new NotBlank()));
    }
    
    public function getName()
    {
        return 'institutionDoctor';
    }
}