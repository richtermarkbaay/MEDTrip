<?php
namespace HealthCareAbroad\ProcedureBundle\Form;

use HealthCareAbroad\ProcedureBundle\Form\DataTransformer\TagToObjectTransformer;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;

class ProcedureType extends AbstractType
{	
	private $em;
	
	public function __construct($em)
	{
		$this->em = $em;
	}
	
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$transformer = new TagToObjectTransformer($this->em);
		
		$builder
			->add('name', 'text')
			->add($builder->create('tags','textarea')->addModelTransformer($transformer));
	}

	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
		$resolver->setDefaults(array(
				'data_class' => 'HealthCareAbroad\ProcedureBundle\Entity\MedicalProcedure',
		));
	}
	
	public function getName()
	{
		return 'procedure';
	}
}