<?php

namespace HealthCareAbroad\FrontendBundle\Form;

use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;
use HealthCareAbroad\FrontendBundle\Entity\NewsletterSubscriber;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class NewsletterSubscriberFormType extends AbstractType
{
public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->add('email','email', array('constraints' => array(new Email(array('message' => 'Please enter valid email address.')), 
		                new NotBlank(array('message' => 'Email is required.'))), 'label' => ' '));
	}

	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
	    $resolver->setDefaults(array(
			'data_class' => 'HealthCareAbroad\FrontendBundle\Entity\NewsletterSubscriber',
		));
	}

	public function getName()
	{
		return 'newsletter_subscriber';
	}
}


