<?php
namespace HealthCareAbroad\HelperBundle\Form;

use HealthCareAbroad\HelperBundle\Form\ListType\GlobalCountryListType;

use Symfony\Component\Validator\Constraints\NotBlank;

use HealthCareAbroad\HelperBundle\Entity\State;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


class StateFormType extends AbstractType
{	
    const NAME = 'geoState';
    
	public function buildForm(FormBuilderInterface $builder, array $options)
	{   
		$builder->add('name');
		$builder->add('geoCountry', GlobalCountryListType::NAME, array('empty_value' => 'Please select a country', 'label' => 'Country'));
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
	    return array(State::STATUS_NEW => 'New', State::STATUS_ACTIVE => 'Active', State::STATUS_INACTIVE => 'Inactive');
	}

	public function getName()
	{
		return self::NAME;
	}
}
