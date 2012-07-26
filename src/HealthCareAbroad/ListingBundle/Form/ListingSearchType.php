<?php
namespace HealthCareAbroad\ListingBundle\Form;

use HealthCareAbroad\HelperBundle\Form\CityListType;

use HealthCareAbroad\HelperBundle\Form\EventListener\LoadCitiesSubscriber;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;

class ListingSearchType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$subscriber = new LoadCitiesSubscriber($builder->getFormFactory());
		$builder->addEventSubscriber($subscriber);		
		
		$builder->add('searchTerm', 'text', array('data' => 'Search'));
		$builder->add('country', 'country_list', array(
				'required' => false, 
				'empty_value' => 'Select Country:', 
				'attr'=>array('onchange'=>'Location.loadCities($(this))')
		));
	}
	
	public function getName()
	{
		return 'listingSearch';
	}
}