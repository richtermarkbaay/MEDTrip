<?php
namespace HealthCareAbroad\HelperBundle\Form\ListType;

use HealthCareAbroad\HelperBundle\Form\DataTransformer\CountryTransformer;

use HealthCareAbroad\HelperBundle\Form\DataTransformer\CountryArrayTransformer;

use Symfony\Component\Form\FormBuilderInterface;

use HealthCareAbroad\HelperBundle\Services\LocationService;

use Doctrine\ORM\EntityRepository;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class CountryListType extends AbstractType 
{	
    /**
     * @var LocationService
     */
    private $locationService;
    
	public function setLocationService(LocationService $service)
	{
	    $this->locationService = $service;
	}
	
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
	    $builder->prependNormTransformer(new CountryTransformer($this->locationService));
	}
	
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {	
        
        $countries = $this->locationService->getGlobalCountryList();
        $choices = array();
        foreach ($countries as $countryArray){
            $choices[$countryArray['id']] = $countryArray['name'];
        }
        
        $resolver->setDefaults(array(
            'choices' => $choices
        	//'property' => 'name',
			//'query_builder' => function(EntityRepository $er){ return $er->getQueryBuilderForCountries(); }
        ));
    }

    public function getParent()
    {
        return 'choice';
    }

    public function getName()
    {
        return 'country_list';
    }
}