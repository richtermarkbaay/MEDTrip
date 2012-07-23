<?php
namespace HealthCareAbroad\ProcedureBundle\Form;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;

class ProcedureType extends AbstractType
{	
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('id', 'hidden')
			->add('name', 'text')
			->add('tags', 'collection',array('type'=>'texteara'));
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