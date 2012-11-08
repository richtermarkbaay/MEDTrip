<?php

namespace HealthCareAbroad\HelperBundle\Form;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormViewInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\DependencyInjection\ContainerInterface;
use HealthCareAbroad\HelperBundle\Form\ListType\CountryListType;
use HealthCareAbroad\HelperBundle\Form\ListType\CityListType;

use Doctrine\Common\Persistence\ObjectManager;

use HealthCareAbroad\HelperBundle\Form\EventListener\LoadCitiesSubscriber;

class InstitutionFormType extends AbstractType
{
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'validation_groups' => array('addInstitutionDetails', 'Default')
        ));
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {	
    	$subscriber = new LoadCitiesSubscriber($builder->getFormFactory());
    	$builder->addEventSubscriber($subscriber);
    	 
    	$countryId = ($country = $builder->getData()->getCountry()) ? $country->getId() : 0;
    	$builder->add('description', 'textarea', array('constraints'=>array(new NotBlank())));
    	$builder->add('country', 'country_list', array('attr' => array('onchange'=>'Location.loadCities($(this))')));
    	$builder->add('city', new CityListType($countryId));
    	$builder->add('zipCode', 'number', array('label' => 'Zip Code'));
    	$builder->add('state', 'text');
    	$builder->add('address1', 'text', array('label' => 'Address'));  	
  
    	$builder->add('contactEmail', 'text', array('constraints' => array( new Email() ),'label' => 'Contact Email'));
    		
    }
    
    public function getName()
    {
        return 'institutionDetails';
    }
    
}