<?php

namespace HealthCareAbroad\InstitutionBundle\Form;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormViewInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\DependencyInjection\ContainerInterface;
use HealthCareAbroad\HelperBundle\Form\ListType\CountryListType;
use HealthCareAbroad\HelperBundle\Form\ListType\CityListType;

use Doctrine\Common\Persistence\ObjectManager;

use HealthCareAbroad\HelperBundle\Form\EventListener\LoadCitiesSubscriber;

class InstitutionProfileType extends AbstractType
{
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
        	'profile_type' => true,
            'validation_groups' => array('addInstitutionInformation', 'Default')
        ));
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {	
    	$subscriber = new LoadCitiesSubscriber($builder->getFormFactory());
    	
    	$builder->addEventSubscriber($subscriber);
    	 
    	$countryId = ($country =$builder->getData()->getCountry()) ? $country->getId() : 0;

    	$builder->add('country', 'country_list', array('attr' => array('onchange'=>'Location.loadCities($(this))')));
    	$builder->add('city', new CityListType($countryId));
    	$builder->add('zipCode', 'integer', array('label' => 'Zip Code'));
    	$builder->add('state', 'text');
    	$builder->add('address1', 'text', array('label' => 'Address'));
    	$builder->add('contactEmail', 'text', array('label' => 'Contact Email'));
    	
    	if ($options['profile_type']) {
    		
	    	$builder->add('coordinates', 'hidden');
	    	$builder->add('institutionSite', 'text', array('label' => 'Institution Website', 'virtual' => true ));
	    	$builder->add('facebook', 'text', array('label' => 'Facebook Page', 'virtual' => true ));
	    	$builder->add('twitter', 'text', array('label' => 'Twitter Account', 'virtual' => true ));
	    	
    	}else{
    		
    		$builder->add('description', 'textarea', array('constraints'=>array(new NotBlank())));
		}
    }
    
    public function getName()
    {
        return 'institutionProfile';
    }
    
}