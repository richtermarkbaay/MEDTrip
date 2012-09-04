<?php
namespace HealthCareAbroad\MedicalProcedureBundle\Form;

use Symfony\Component\Validator\Constraints\NotBlank;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use HealthCareAbroad\MedicalProcedureBundle\Form\DataTransformer\MedicalCenterStatusToBooleanTransformer;

class MedicalCenterType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->add('name');
		$builder->add('description');
		$builder->add('status', 'medicalCenterStatusSelector');
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