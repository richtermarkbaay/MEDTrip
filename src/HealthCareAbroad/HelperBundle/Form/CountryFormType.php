<?php
namespace HealthCareAbroad\HelperBundle\Form;

use Symfony\Component\Validator\Constraints\NotBlank;

use HealthCareAbroad\HelperBundle\Entity\Country;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


class CountryFormType extends AbstractType
{
    const NAME = 'geoCountry';
    
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->add('name', 'text');
		$builder->add('ccIso', 'text', array('label' => 'Abbr (CCISO)'));
		$builder->add('countryCode', 'text');
		$builder->add('status', 'choice', array('choices'=>$this->_getStatuses()));
	}

	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
	    $resolver->setDefaults(array('csrf_protection' => false));
	}

	public function getName()
	{
		return self::NAME;
	}
	
	private function _getStatuses()
	{
	    return array(Country::STATUS_NEW => 'New', Country::STATUS_ACTIVE => 'Active', Country::STATUS_INACTIVE => 'Inactive');
	}
}