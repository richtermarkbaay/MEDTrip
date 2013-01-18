<?php
/**
 * 
 * @author adelbertsilla
 *
 */
namespace HealthCareAbroad\AdvertisementBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class HighlightFeaturedImagesFormType extends AbstractType
{   
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->add('image0', 'file', array('label' => 'Image 1'));
		$builder->add('image1', 'file', array('label' => 'Image 2'));
		$builder->add('image2', 'file', array('label' => 'Image 3'));
	}

	// How does it work?
	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
	    $resolver->setDefaults(array('virtual' => true));
	}

	public function getName()
	{
		return 'highlight_featured_images';
	}
}