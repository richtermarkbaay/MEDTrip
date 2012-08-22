<?php
namespace HealthCareAbroad\MedicalProcedureBundle\Form;

use Symfony\Component\Validator\Constraints\NotBlank;

use HealthCareAbroad\MedicalProcedureBundle\Entity\MedicalProcedure;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


class MedicalProcedureFormType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$status = array(
			MedicalProcedure::STATUS_ACTIVE => 'active',
			MedicalProcedure::STATUS_INACTIVE => 'inactive'
		);

		$builder->add('name', 'text', array('constraints'=>array(new NotBlank())));
		$builder->add('medicalProcedureType', 'medicalproceduretype_list', array('constraints'=>array(new NotBlank())));
		$builder->add('status', 'choice', array('choices' => $status), array('constraints'=>array(new NotBlank())));
	}

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
