<?php

namespace HealthCareAbroad\FrontendBundle\Form;

use HealthCareAbroad\FrontendBundle\Form\ListType\InquirySubjectListType;

use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormViewInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use HealthCareAbroad\HelperBundle\Form\FieldType\LocationFieldType;

use HealthCareAbroad\HelperBundle\Form\EventListener\LoadCitiesSubscriber;
class InquiryType extends AbstractType
{
	private $container;
	
	public function setContainer(ContainerInterface $container = null) {
		$this->container = $container;
	}
	public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $cityId = 0;
    	$builder
    		->add('firstName', 'text', array('error_bubbling' => true))
    		->add('lastName', 'text', array('error_bubbling' => true))
    		->add('country', 'globalCountry_list', array('empty_value' => 'Please select a country', 'attr' => array('onchange'=>'Location.loadCities($(this), '. $cityId . ')')))
    		->add('city','city_list')
    		->add('contactNumber','text')
    		->add('email', 'email', array('error_bubbling' => true))
    		->add('message', 'textarea', array('error_bubbling' => true))
    		;
    }
    
    public function getName()
    {
        return 'inquire';
    }
}
