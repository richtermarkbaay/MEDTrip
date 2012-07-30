<?php
namespace HealthCareAbroad\MedicalProcedureBundle\Form\ListType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class MedicalProcedureTypeListType extends AbstractType 
{	
	private $container;

	public function setContainer(ContainerInterface $container = null) {
		$this->container = $container;
	}
	
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {	
        $resolver->setDefaults(array(
        	'property' => 'name',
        	'label' => 'Procedure Type',
			'class' => 'HealthCareAbroad\MedicalProcedureBundle\Entity\MedicalProcedureType',
			'query_builder' => $this->container->get("services.medical_procedure")->getMedicalProcedureTypes()
        ));
    }
   
    public function getParent()
    {
        return 'entity';
    }

    public function getName()
    {
        return 'medicalproceduretype_list';
    }
}