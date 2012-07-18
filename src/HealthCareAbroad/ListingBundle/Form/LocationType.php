<?php
namespace HealthCareAbroad\ListingBundle\Form;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;

class LocationType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('country', 'country_list')
			->add('city', 'city_list')
			->add('address', 'textarea');
	}

	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
		$resolver->setDefaults(array(
				'data_class' => 'HealthCareAbroad\ListingBundle\Entity\ListingLocation',
		));
	}
	
	public function getName()
	{
		return 'location';
	}
}