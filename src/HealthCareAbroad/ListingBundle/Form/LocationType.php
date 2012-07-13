<?php
namespace HealthCareAbroad\ListingBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;

class LocationType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
		->add('address', 'textarea')
		->add('zipcode', 'text')
		->add('city', 'text')
		->add('country', 'text')
		;
	}

	public function getName()
	{
		return 'location';
	}
}