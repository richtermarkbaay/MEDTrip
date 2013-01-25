<?php
/**
 * 
 * @author Adelbert D. Silla
 *
 */
namespace HealthCareAbroad\AdvertisementBundle\Form;

use HealthCareAbroad\AdvertisementBundle\Entity\AdvertisementHighlightType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AdvertisementHighlightsFormType extends AbstractType
{   
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
	    $builder->add($builder->create(new HighlightFormType()));
	}

	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
	    $resolver->setDefaults(array('virtual' => true));
	}

	public function getParent()
	{
	    return 'collection';
	}

	public function getName()
	{
		return 'advertisement_highlights';
	}
}