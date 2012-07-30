<?php
namespace HealthCareAbroad\InstitutionBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;


class InstitutionListType extends AbstractType 
{	
	private $container;

	public function setContainer(ContainerInterface $container = null) {
		$this->container = $container;
	}
	
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {	
        $resolver->setDefaults(array(
        	'property' => 'name',
			'class' => 'HealthCareAbroad\InstitutionBundle\Entity\Institution',
			'query_builder' => $this->container->get("services.provider")->getInstitutions()
        ));
    }
   
    public function getParent()
    {
        return 'entity';
    }

    public function getName()
    {
        return 'institution_list';
    }
}