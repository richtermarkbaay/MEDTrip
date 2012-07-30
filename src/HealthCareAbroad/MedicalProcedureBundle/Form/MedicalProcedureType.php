<?php
namespace HealthCareAbroad\MedicalProcedureBundle\Form;

use HealthCareAbroad\MedicalProcedureBundle\Entity\MedicalProcedure;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


class MedicalProcedureType extends AbstractType
{
	private $id;

	public function __construct($id = '')
	{
		$this->id = $id;
	}

	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->add('id', 'hidden', array('virtual'=>true, 'attr' => array('value' => $this->id)));
		$builder->add('name', 'text');
		$builder->add('medical_procedure_type', 'medicalproceduretype_list');
		$builder->add('status', 'choice', array('choices' => array_keys(MedicalProcedure::$STATUS)));
	}

	// How does it work?
	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
	    $resolver->setDefaults(array(
			'data_class' => 'HealthCareAbroad\MedicalProcedureBundle\Entity\MedicalProcedure',
		));
	}

	public function getName()
	{
		return 'medicalProcedure';
	}
}
