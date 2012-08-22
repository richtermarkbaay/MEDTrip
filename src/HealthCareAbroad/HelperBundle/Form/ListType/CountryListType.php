<?php
namespace HealthCareAbroad\HelperBundle\Form\ListType;

use Doctrine\ORM\EntityRepository;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class CountryListType extends AbstractType 
{	
	private $container;

	public function setContainer(ContainerInterface $container = null) {
		$this->container = $container;
	}
	
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {	
        $resolver->setDefaults(array(
        	'property' => 'name',
			'class' => 'HealthCareAbroad\HelperBundle\Entity\Country',
			'query_builder' => function(EntityRepository $er){ return $er->getQueryBuilderForCountries(); }
        ));
    }

    public function getParent()
    {
        return 'entity';
    }

    public function getName()
    {
        return 'country_list';
    }
}