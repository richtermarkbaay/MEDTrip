<?php
namespace HealthCareAbroad\HelperBundle\Form;

use Symfony\Component\Validator\Constraints\NotBlank;

use HealthCareAbroad\HelperBundle\Entity\City;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


class CityFormType extends AbstractType
{	
    const NAME = 'geoCity';
    
	public function buildForm(FormBuilderInterface $builder, array $options)
	{   
		$builder->add('name');
		$builder->add('geoCountry', 'globalCountry_list', array('empty_value' => 'Please select a country'));
		$builder->add('geoState', 'choice', array('choices' => array(null => 'Please select a state')));
		$builder->add('status', 'choice', array('choices' => $this->getStatuses()));
	}

	// How does it work?
	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
	    $resolver->setDefaults(array(
            'csrf_protection' => false
		));
	}
	
	private function getStatuses()
	{
	    return array(City::STATUS_NEW => 'New', City::STATUS_ACTIVE => 'Active', City::STATUS_INACTIVE => 'Inactive');
	}

	public function getName()
	{
		return self::NAME;
	}
}
