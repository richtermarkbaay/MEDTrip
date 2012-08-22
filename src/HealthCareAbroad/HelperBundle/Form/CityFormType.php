<?php
namespace HealthCareAbroad\HelperBundle\Form;

use Symfony\Component\Validator\Constraints\NotBlank;

use HealthCareAbroad\HelperBundle\Entity\City;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


class CityFormType extends AbstractType
{	
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$status = array(City::STATUS_ACTIVE => 'active', City::STATUS_INACTIVE => 'inactive');

		$builder->add('name', 'text', array('constraints'=>array(new NotBlank())));
		$builder->add('country', 'country_list');
		$builder->add('status', 'choice', array('choices'=>$status));
	}

	// How does it work?
	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
	    $resolver->setDefaults(array(
			'data_class' => 'HealthCareAbroad\HelperBundle\Entity\City',
		));
	}

	public function getName()
	{
		return 'city';
	}
}
