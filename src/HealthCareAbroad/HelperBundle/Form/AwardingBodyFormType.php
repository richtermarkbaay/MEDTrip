<?php
namespace HealthCareAbroad\HelperBundle\Form;

use HealthCareAbroad\HelperBundle\Entity\AwardingBody;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AwardingBodyFormType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$status = array(
			AwardingBody::STATUS_ACTIVE => 'active',
			AwardingBody::STATUS_INACTIVE => 'inactive',
		);

		$builder->add('name', 'text', array('constraints'=>array(new NotBlank())));
		$builder->add('details', 'textarea', array('constraints'=>array(new NotBlank())));
		$builder->add('website', 'text');
		$builder->add('status', 'choice', array('choices'=>$status));
	}

	// How does it work?
	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
	    $resolver->setDefaults(array(
			'data_class' => 'HealthCareAbroad\HelperBundle\Entity\AwardingBody',
		));
	}

	public function getName()
	{
		return 'awardingBody';
	}
}
