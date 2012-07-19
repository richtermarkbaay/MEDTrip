<?php
namespace HealthCareAbroad\ProviderBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;


class ProviderListType extends AbstractType 
{	
	private $container;

	public function setContainer(ContainerInterface $container = null) {
		$this->container = $container;
	}
	
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {	
        $resolver->setDefaults(array(
        	'property' => 'name',
			'class' => 'HealthCareAbroad\ProviderBundle\Entity\Provider',
			'query_builder' => $this->container->get("services.provider")->getProviders()
        ));
    }
   
    public function getParent()
    {
        return 'entity';
    }

    public function getName()
    {
        return 'provider_list';
    }
}