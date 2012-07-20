<?php
namespace HealthCareAbroad\ListingBundle\Form;

use HealthCareAbroad\HelperBundle\Form\EventListener\LoadCitiesSubscriber;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;

class LocationType extends AbstractType
{
	
	public function buildForm(FormBuilderInterface $builder, array $options)
	{

		$subscriber = new LoadCitiesSubscriber($builder->getFormFactory());
		$builder->addEventSubscriber($subscriber);

		$builder
			->add('id', 'hidden')
			->add('country', 'country_list', array('attr'=>array('onchange'=>'Location.loadCities($(this))')))
			//->add('city', 'city_list', array('disabled'=>false, 'empty_value'=>''))
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