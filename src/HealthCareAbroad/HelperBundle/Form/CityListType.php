<?php
namespace HealthCareAbroad\HelperBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class CityListType extends AbstractType 
{	
	private $container;

	public function setContainer(ContainerInterface $container = null) {
		$this->container = $container;
	}
	
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {	
        $resolver->setDefaults(array(
        	'property' => 'name',
			'class' => 'HelperBundle:City',
			'query_builder' => $this->container->get("services.location")->getActiveCities()
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