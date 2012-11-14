<?php

namespace HealthCareAbroad\InstitutionBundle\Form;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use HealthCareAbroad\InstitutionBundle\Form\ListType\InstitutionOfferedServiceListType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormViewInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\MinLength;
use Symfony\Component\Validator\Constraints\MaxLength;
use Symfony\Component\DependencyInjection\ContainerInterface;
use HealthCareAbroad\HelperBundle\Form\ListType\CountryListType;
use HealthCareAbroad\HelperBundle\Form\ListType\CityListType;

use Doctrine\Common\Persistence\ObjectManager;

use HealthCareAbroad\InstitutionBundle\Form\ListType\LanguageListType;
use HealthCareAbroad\InstitutionBundle\Form\Transformer\LanguageTransformer;
use HealthCareAbroad\HelperBundle\Form\EventListener\LoadCitiesSubscriber;

class InstitutionDetailType extends AbstractType
{
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
			'hidden_field' => true,
        	'profile_type' => true,
            'validation_groups' => array('editInstitutionInformation', 'Default')
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
    	$builder->add('contactEmail', 'text', array('label' => 'Contact Email'));
    	$builder->add('address1', 'text', array('label' => 'Address'));
   
		if ($options['profile_type']) {
    		
			$builder->add('name', 'text', array('constraints'=>array(new NotBlank())));
			$builder->add('institutionLanguagesSpoken','language_autocomplete', array('constraints' => new NotBlank(),'label' => ' '));
			$builder->add('institutionOfferedServices', new InstitutionOfferedServiceListType(), array('expanded' => true,'multiple' => true));
			$builder->add('description', 'textarea');
			$builder->add('contactNumber', 'hidden');
		}
		if ($options['hidden_field']) {
			
			$builder->add('description', 'textarea');
			 
		}
    }
    
    public function getName()
    {
        return 'institutionDetail';
    }
    
    
}