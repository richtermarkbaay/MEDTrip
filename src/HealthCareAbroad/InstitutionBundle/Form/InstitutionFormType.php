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

class InstitutionFormType extends AbstractType
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
    		->add('name', 'text')
    		->add('description', 'textarea', array('attr' => array('maxLength' => 500)))
    		->add('country', 'country_list', array('attr' => array('onchange'=>'Location.loadCities($(this))')))
    		->add('city', new CityListType(1))
    		->add('address1','text', array('label' => 'Address Line 1'))
    		->add('address2','text', array('label' => 'Address Line 2'))
    		;
    }
    
    public function getName()
    {
        return 'institution';
    }
}