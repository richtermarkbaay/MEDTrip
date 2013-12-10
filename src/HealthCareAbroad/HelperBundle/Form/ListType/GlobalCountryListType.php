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

class GlobalCountryListType extends AbstractType 
{	
    const NAME = 'globalCountry_list';
    
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
	    //$builder->prependNormTransformer(new CountryTransformer($this->locationService));
	    //$builder->addModelTransformer(new CountryTransformer($this->locationService));	    
	}
	
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {	
        $countries = $this->locationService->getGlobalCountries();
        $choices = array();
        foreach ($countries['data'] as $country){
            $choices[$country['id']] = $country['name'];
        }
        
        $resolver->setDefaults(array('choices' => $choices));
    }

    public function getParent()
    {
        return 'choice';
    }

    public function getName()
    {
        return self::NAME;
    }
}