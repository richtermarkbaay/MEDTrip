<?php
namespace HealthCareAbroad\TreatmentBundle\Form;

use HealthCareAbroad\TreatmentBundle\Entity\Specialization;

use Symfony\Component\Validator\Constraints\NotBlank;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use HealthCareAbroad\TreatmentBundle\Form\DataTransformer\SpecializationStatusToBooleanTransformer;

class SpecializationType extends AbstractType
{	
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$medicalCenter = $options['data'];

		$status = array(
			Specialization::STATUS_ACTIVE => 'active',
			Specialization::STATUS_INACTIVE => 'inactive'
		);

		$builder->add('name');
		$builder->add('description');
		$builder->add('status', 'choice', array('choices' => $status));
	}
	
	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
		$resolver->setDefaults(array(
			'data_class' => 'HealthCareAbroad\TreatmentBundle\Entity\Specialization'
		));
	}	

	public function getName()
	{
		return 'medicalCenter';
	}
}