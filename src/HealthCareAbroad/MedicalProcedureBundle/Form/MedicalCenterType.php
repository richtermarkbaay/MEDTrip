<?php
namespace HealthCareAbroad\MedicalProcedureBundle\Form;

use HealthCareAbroad\MedicalProcedureBundle\Entity\MedicalCenter;

use Symfony\Component\Validator\Constraints\NotBlank;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use HealthCareAbroad\MedicalProcedureBundle\Form\DataTransformer\MedicalCenterStatusToBooleanTransformer;

class MedicalCenterType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$status = array(
			MedicalCenter::STATUS_ACTIVE => 'active',
			MedicalCenter::STATUS_INACTIVE => 'inactive'
		);

		$builder->add('name');
		$builder->add('description');
		$builder->add('status', 'choice', array('choices' => $status));
	}
	
	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
		$resolver->setDefaults(array(
			'data_class' => 'HealthCareAbroad\MedicalProcedureBundle\Entity\MedicalCenter'
		));
	}	

	public function getName()
	{
		return 'medicalCenter';
	}
}