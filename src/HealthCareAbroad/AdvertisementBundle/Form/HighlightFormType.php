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

class HighlightFormType extends AbstractType
{   
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
	    $builder->add('type', 'choice', array('choices' => AdvertisementHighlightType::getList()));
		$builder->add('header', 'text', array('label' => 'Header (optional)'));
		$builder->add('label', 'text');
		try {
		    $builder->add('icon', 'file', array('data_class' => 'HealthCareAbroad\MediaBundle\Entity\Media'));		    
		}catch(\Exception $e) { var_dump('bert'); exit; }

	}

	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
	    $resolver->setDefaults(array('virtual' => true));
	}

	public function getName()
	{
		return 'advertisement_highlight';
	}
}