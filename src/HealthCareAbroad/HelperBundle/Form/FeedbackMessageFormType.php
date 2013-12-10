<?php
namespace HealthCareAbroad\HelperBundle\Form;

use HealthCareAbroad\HelperBundle\Form\FieldType\FancyCountryFieldType;

use Symfony\Component\Validator\Constraints\NotBlank;

use HealthCareAbroad\HelperBundle\Entity\FeedbackMessage;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FeedbackMessageFormType extends AbstractType
{	
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->add('name', 'text', array('label' => 'Youre Name'));
		$builder->add('emailAddress', 'text', array('label' => 'Your Email Address '));
		$builder->add('message', 'textarea', array('label' => 'Enter your message '));
		$builder->add('country', FancyCountryFieldType::NAME, array('label' => 'Your Country'));
		$builder->add('captcha', 'captcha', array('label' => 'Please type the code '));
	}

	// How does it work?
	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
	    $resolver->setDefaults(array(
			'data_class' => 'HealthCareAbroad\HelperBundle\Entity\FeedbackMessage',
		));
	}

	public function getName()
	{
		return 'feedbackMessage';
	}
}
