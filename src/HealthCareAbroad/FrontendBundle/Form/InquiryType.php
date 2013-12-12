<?php
namespace HealthCareAbroad\FrontendBundle\Form;

use HealthCareAbroad\HelperBundle\Form\FieldType\FancyCountryFieldType;

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
    	    ->add('inquirySubject', 'inquiry_subject_list', array('expanded' => true,'multiple' => false,'constraints' => array(new NotBlank(array('message' => 'Please choose at least one from Inquiry Subject')))))
    		->add('firstName', 'text')
    		->add('lastName', 'text')
    		->add('clinicName', 'text')
    		->add('country', FancyCountryFieldType::NAME)
    		->add('contactNumber','text')
    		->add('email', 'email')
    		->add('message', 'textarea')
    		->add('captcha', 'captcha', array('label'=>'Please type the code *'))
    		;
    }
    
    public function getName()
    {
        return 'inquire';
    }
}
