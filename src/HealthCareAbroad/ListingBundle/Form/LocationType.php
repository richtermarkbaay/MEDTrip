<?php
namespace HealthCareAbroad\ListingBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;

class LocationType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->add('country', 'country_list')->add('city', 'city_list')->add('address', 'textarea');
	}

	public function getName()
	{
		return 'location';
	}
}