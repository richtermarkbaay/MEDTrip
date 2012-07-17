<?php
namespace HealthCareAbroad\ProcedureBundle\Form;

use HealthCareAbroad\ProcedureBundle\Entity\MedicalProcedure;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityManager;

class ProcedureListType extends AbstractType
{
	private $entityManager;
	
	public function __construct(EntityManager $entityManager)
	{
		$this->entityManager = $entityManager;
	}

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
    	$result = $this->entityManager->getRepository('ProcedureBundle:MedicalProcedure')->findByStatus(1);
    	$procedures = array();
    	
    	foreach($result as $each) {
    		$procedures[$each->getId()] = $each->getName();
    	}
 
        $resolver->setDefaults(array(
            'choices' => $procedures
        ));
    }

    public function getParent()
    {
        return 'choice';
    }

    public function getName()
    {
        return 'procedure_list';
    }
}