<?php

namespace HealthCareAbroad\InstitutionBundle\Form;

use HealthCareAbroad\HelperBundle\Form\ListType\CountryListType;
use HealthCareAbroad\HelperBundle\Form\ListType\CityListType;
use HealthCareAbroad\HelperBundle\Validator\Constraints\EqualFieldValue;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Form\AbstractType;
use HealthCareAbroad\HelperBundle\Form\EventListener\LoadCitiesSubscriber;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormViewInterface;

class InstitutionType extends AbstractType
{
	private $container;
	
	public function setContainer(ContainerInterface $container = null) {
		$this->container = $container;
	}
	
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    	$subscriber = new LoadCitiesSubscriber($builder->getFormFactory());
    	$builder->addEventSubscriber($subscriber);
    	
    	$builder
    		->add('name', 'text', array('constraints' => new NotBlank()))
    		->add('description', 'textarea', array('constraints' => new NotBlank()))
    		->add('firstName', 'text', array('label' => 'First name', 'constraints' => new NotBlank()))
    		->add('middleName', 'text', array('label' => 'Middle name'))
    		->add('lastName', 'text', array('label' => 'Last name', 'constraints' => new NotBlank()))
    		->add('email','email', array('constraints' => array(new Email(), new NotBlank())))
    		->add( 'new_password', 'password', array(
                    'label' => 'Password', 
                    'virtual' => true, 
                    'constraints' => array(new NotBlank())
                ))
    	    ->add('confirm_password', 'password', array(
                    'label' => 'Confirm Password', 
                    'virtual' => true, 
                    'constraints' => array(
                        new EqualFieldValue(array('field' => 'new_password', 'message' => 'Passwords do not match')))
                ))
    		->add('country', 'country_list', array('attr' => array('onchange'=>'Location.loadCities($(this))')))
    		->add('city', new CityListType(1))
    		->add('address1','text', array('label' => 'Address Line 1', 'constraints' => new NotBlank()))
    		->add('address2','text', array('label' => 'Address Line 2', 'constraints' => new NotBlank()))
    		;
    }
    
    public function getName()
    {
        return 'institution';
    }
}