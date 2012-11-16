<?php
namespace HealthCareAbroad\HelperBundle\Form;

use HealthCareAbroad\HelperBundle\Entity\AwardingBodies;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


class AwardingBodiesFormType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$status = array(
			AwardingBodies::STATUS_ACTIVE => 'active',
			AwardingBodies::STATUS_INACTIVE => 'inactive',
		);

		$builder->add('name', 'text');
		$builder->add('details', 'textarea', array('constraints'=>array(new NotBlank())));
		$builder->add('website', 'text', array('constraints'=>array(new NotBlank())));
		$builder->add('status', 'choice', array('choices'=>$status));
	}

	// How does it work?
	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
	    $resolver->setDefaults(array(
			'data_class' => 'HealthCareAbroad\HelperBundle\Entity\AwardingBodies',
		));
	}

	public function getName()
	{
		return 'awards';
	}
}
