<?php
namespace HealthCareAbroad\ListingBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\AbstractType;

class ListingType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->add('title');
		$builder->add('description');
	}

	public function getName()
	{
		return 'listing';
	}
}
