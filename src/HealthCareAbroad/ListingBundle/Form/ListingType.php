<?php
namespace HealthCareAbroad\ListingBundle\Form;

use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Blank;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


class ListingType extends AbstractType
{	
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$isProvider = false;
		if(!$isProvider) 
			$builder->add('provider', 'provider_list');

		$builder->add('procedure', 'procedure_list');
		$builder->add('title');
		$builder->add('description');
		$builder->add('logo', 'file', array('required'=>false, 'constraints'=>array(new Blank())));
		$builder->add('locations', 'collection', array('type' =>new LocationType(),'allow_add'=>true,'by_reference'=>false));
	}

	// How does it work?
	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
	    $resolver->setDefaults(array(
			'data_class' => 'HealthCareAbroad\ListingBundle\Entity\Listing',
		));
	}

	public function getName()
	{
		return 'listing';
	}
}
