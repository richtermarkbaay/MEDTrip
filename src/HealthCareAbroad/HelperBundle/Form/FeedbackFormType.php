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

		$builder->add('subject', 'text', array('constraints'=>array(new NotBlank())));
		$builder->add('message', 'textarea', array('constraints'=>array(new NotBlank())));
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
