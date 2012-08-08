<?php
namespace HealthCareAbroad\MedicalProcedureBundle\Form;

use Symfony\Component\Validator\Constraints\NotBlank;

use HealthCareAbroad\MedicalProcedureBundle\Entity\MedicalProcedure;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


class MedicalProcedureType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->add('name', 'text', array('constraints'=>array(new NotBlank())));
		$builder->add('medical_procedure_type', 'medicalproceduretype_list', array('constraints'=>array(new NotBlank())));
		$builder->add('status', 'choice', array('choices' => array_keys(MedicalProcedure::$STATUS)), array('constraints'=>array(new NotBlank())));
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
