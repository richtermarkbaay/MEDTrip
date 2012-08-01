<?php
namespace HealthCareAbroad\MedicalProcedureBundle\Form;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class MedicalCenterType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->add('name');
		$builder->add('description');
		$builder->add('status');
	}
	
	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
		$resolver->setDefaults(array(
			'data_class' => 'HealthCareAbroad\MedicalProcedureBundle\Entity\MedicalCenter',
			'validation_groups' => array('default')
		));
	}	
	
	public function getName()
	{
		return 'medicalCenter';
	}
}