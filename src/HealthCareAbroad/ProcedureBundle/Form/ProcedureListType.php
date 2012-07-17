<?php
namespace HealthCareAbroad\ProcedureBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ProcedureListType extends AbstractType
{
	private $container;

	public function setContainer(ContainerInterface $container = null) {
		$this->container = $container;
	}

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
			$resolver->setDefaults(array(
			'property' => 'name',
			'class' => 'ProcedureBundle:MedicalProcedure',
			'query_builder' => $this->container->get("services.procedure")->getActiveProcedures()
		));
    }

    public function getParent()
    {
        return 'entity';
    }

    public function getName()
    {
        return 'procedure_list';
    }
}