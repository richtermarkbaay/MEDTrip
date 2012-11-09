<?php

namespace HealthCareAbroad\DoctorBundle\Form;

use HealthCareAbroad\TreatmentBundle\Form\ListType\SpecializationListType;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormViewInterface;

class DoctorFormType extends AbstractType
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
     		->add('lastName', 'text', array('label' => 'Last name', 'constraints' => new NotBlank()))
    	    ->add('specializations', new SpecializationListType(), array('expanded' => true,'multiple' => true))
    	    ->add('contactEmail', 'text', array('label' => 'Contact Email'))
    	    ->add('contactNumber', 'hidden', array('label' => 'Contact Number'));
    }
    
    public function getName()
    {
        return 'doctor';
    }
}