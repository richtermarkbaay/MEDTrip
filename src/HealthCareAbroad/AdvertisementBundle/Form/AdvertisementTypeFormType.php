<?php
namespace HealthCareAbroad\AdvertisementBundle\Form;

use HealthCareAbroad\AdvertisementBundle\Form\DataTransformer\AdvertisementTypeConfigurationTransformer;

use HealthCareAbroad\AdvertisementBundle\Form\ListType\AdvertisementPropertyNameListType;

use Symfony\Component\Validator\Constraints\NotBlank;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


class AdvertisementTypeFormType extends AbstractType
{   
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->add('name', 'text', array('label' => 'Name: '));

		$builder->add('title', 'checkbox', array('virtual' => true, 'attr' => array('checked' => true, 'disabled' => true)));
		$builder->add('description', 'checkbox', array('virtual' => true, 'attr' => array('checked' => true, 'disabled' => true)));
		
		$builder->add('advertisementTypeConfigurations', 'advertisementPropertyName_list');
	}

	// How does it work?
	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
	    $resolver->setDefaults(array(
			'data_class' => 'HealthCareAbroad\AdvertisementBundle\Entity\AdvertisementType',
		));
	}

	public function getName()
	{
		return 'advertisementType';
	}
}
