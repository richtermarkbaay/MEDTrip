<?php
namespace HealthCareAbroad\HelperBundle\Form;

use Symfony\Component\Validator\Constraints\NotBlank;

use HealthCareAbroad\HelperBundle\Entity\Feedback;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FeedbackFormType extends AbstractType
{	
	public function buildForm(FormBuilderInterface $builder, array $options)
	{

		$builder->add('subject', 'text',array('error_bubbling' => false));
		$builder->add('message', 'textarea',array('error_bubbling' => false));
	}

	// How does it work?
	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
	    $resolver->setDefaults(array(
			'data_class' => 'HealthCareAbroad\HelperBundle\Entity\Feedback',
		));
	}

	public function getName()
	{
		return 'feedback';
	}
}
