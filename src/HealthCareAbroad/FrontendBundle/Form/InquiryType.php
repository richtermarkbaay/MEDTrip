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
    	    ->add('inquirySubject', 'inquiry_subject_list',array('error_bubbling' => false, 'expanded' => true,'multiple' => false,'constraints' => array(new NotBlank(array('message' => 'Please choose at least one from Inquiry Subjects')))))
    		->add('firstName', 'text', array('error_bubbling' => false))
    		->add('lastName', 'text', array('error_bubbling' => false))
    		->add('country', 'globalCountry_list', array('empty_value' => 'Please select a country', 'attr' => array('onchange'=>'Location.loadCities($(this), '. $cityId . ')')))
    		->add('city','city_list', array('empty_value' => 'Select city'))
    		->add('contactNumber','text')
    		->add('email', 'email', array('error_bubbling' => false))
    		->add('message', 'textarea', array('error_bubbling' => false))
    		->add('inquiryCheck', 'checkbox',array('error_bubbling' => false, 'virtual' => true, 'constraints' => array(new NotBlank(array('message' => 'Please agree to Terms of Use and Privacy Policy.')))))
    		;
    }
    
    public function getName()
    {
        return 'inquire';
    }
}
