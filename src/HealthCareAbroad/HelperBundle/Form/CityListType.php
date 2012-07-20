<?php
namespace HealthCareAbroad\HelperBundle\Form;

use HealthCareAbroad\HelperBundle\Entity\Country;

use Doctrine\ORM\EntityRepository;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class CityListType extends AbstractType 
{	
	private $container;
	private $countryId;
	
	public function __construct($countryId)
	{
		$this->countryId = $countryId;		
	}
	
	public function setContainer(ContainerInterface $container = null) {
		$this->container = $container;
	}
	
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
    	$countryId = $this->countryId;
        $resolver->setDefaults(array(
        	'property' => 'name',
			'class' => 'HealthCareAbroad\HelperBundle\Entity\City',
        	'query_builder' => function(EntityRepository $er) use ($countryId) { 
        		return $er->createQueryBuilder('u')
        			->add('where', 'u.status = 1 AND u.country = :country')
        			->setParameter('country', $countryId)
        			->orderBy('u.name', 'ASC'); 
        	}
        ));
    }
   
    public function getParent()
    {
        return 'entity';
    }

    public function getName()
    {
        return 'city_list';
    }
}