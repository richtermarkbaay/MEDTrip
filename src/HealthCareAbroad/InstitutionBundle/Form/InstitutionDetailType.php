<?php

namespace HealthCareAbroad\InstitutionBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormViewInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

use HealthCareAbroad\HelperBundle\Form\ListType\CountryListType;
use HealthCareAbroad\HelperBundle\Form\ListType\CityListType;

use HealthCareAbroad\HelperBundle\Form\EventListener\LoadCitiesSubscriber;

class InstitutionDetailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    	$subscriber = new LoadCitiesSubscriber($builder->getFormFactory());
    	$builder->addEventSubscriber($subscriber);
    	$countryId = $builder->getData()->getCountry()->getId();
    	
    	$builder
    	->add('name', 'text', array('constraints' => new NotBlank()));
    	$builder->add('description', 'textarea', array('constraints' => new NotBlank()));
    	$builder->add('country', 'country_list', array('attr' => array('onchange'=>'Location.loadCities($(this))')));
    	$builder->add('city', new CityListType($countryId));
    	$builder->add('address1','text', array('constraints' => new NotBlank()));
    	$builder->add('address2','text', array('constraints' => new NotBlank()));
    }
    
    public function getName()
    {
        return 'institutionDetail';
    }
    
}