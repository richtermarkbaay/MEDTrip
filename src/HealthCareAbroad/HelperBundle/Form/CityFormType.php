<?php
namespace HealthCareAbroad\HelperBundle\Form;

use HealthCareAbroad\HelperBundle\Form\ListType\GlobalCountryListType;

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
		$builder->add('geoCountry', GlobalCountryListType::NAME, array('empty_value' => 'Please select a country', 'label' => 'Country'));
		$builder->add('geoState', 'choice', array('choices' => array(null => 'Please select a state'), 'label' => 'State'));
		$builder->add('status', 'choice', array('choices' => $this->getStatuses()));
	}

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
