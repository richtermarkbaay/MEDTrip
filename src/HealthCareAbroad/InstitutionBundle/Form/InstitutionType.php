<?php

namespace HealthCareAbroad\InstitutionBundle\Form;

use HealthCareAbroad\HelperBundle\Form\ListType\CountryListType;

use HealthCareAbroad\HelperBundle\Form\ListType\CityListType;

//use HealthCareAbroad\HelperBundle\Entity\Country;

//use HealthCareAbroad\HelperBundle\Services\LocationService;

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
    		->add('firstName', 'text', array('constraints' => new NotBlank()))
    		->add('middleName', 'text', array('constraints' => new NotBlank()))
    		->add('lastName', 'text', array('constraints' => new NotBlank()))
    		->add('email','email', array('constraints' => new Email()))
    		->add( 'new_password', 'password', array(
                    'label' => 'New Password', 
                    'virtual' => true, 
                    'constraints' => array(new NotBlank())
                ))
    	    ->add('confirm_password', 'password', array(
                    'label' => 'Confirm Password', 
                    'virtual' => true, 
                    'constraints' => array(
                        new EqualFieldValue(array('field' => 'new_password', 'message' => 'Passwords do not match')))
                ))
    		->add('country', 'country_list', array('attr'=>array('onselect'=>'Institution.loadCities($(this))')));
    }
    
    public function getName()
    {
        return 'institution';
    }
}