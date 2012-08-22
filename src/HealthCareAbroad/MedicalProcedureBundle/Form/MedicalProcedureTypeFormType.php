<?php
namespace HealthCareAbroad\MedicalProcedureBundle\Form;

use HealthCareAbroad\MedicalProcedureBundle\Entity\MedicalProcedureType;

use Symfony\Component\Validator\Constraints\NotBlank;

use HealthCareAbroad\MedicalProcedureBundle\Entity\MedicalProcedureType as MedicalProcedureTypeEntity;

use Doctrine\ORM\EntityManager;

use HealthCareAbroad\MedicalProcedureBundle\Form\DataTransformer\MedicalCentersTransformer;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


class MedicalProcedureTypeFormType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		
		$status = array(
			MedicalProcedureType::STATUS_ACTIVE => 'active',
			MedicalProcedureType::STATUS_INACTIVE => 'inactive'
		);
		
		$commonConstraints = array(new NotBlank());

		$builder->add('name', 'text', array('constraints'=> $commonConstraints));
		$builder->add('description', 'textarea', array('constraints'=> $commonConstraints));
		$builder->add($builder->create('medicalCenter', 'medicalCenter_list'));
		$builder->add('status', 'choice', array('choices' => $status));
	}

	// How does it work?
	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
	    $resolver->setDefaults(array(
			'data_class' => 'HealthCareAbroad\MedicalProcedureBundle\Entity\MedicalProcedureType',
		));
	}

	public function getName()
	{
		return 'medicalProcedureType';
	}
}
