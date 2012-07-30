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
		//$builder->add('slug');
		$builder->add('status', 'checkbox', array(
			'label'     => 'Activate medical center?',
			'required'  => false
		));		
		
		//no mapping to entity
		//$builder->add('dueDate', null, array('property_path' => false));
	}

	
	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
		$resolver->setDefaults(array(
			'data_class' => 'HealthCareAbroad\MedicalProcedureBundle\Entity\MedicalCenter',
		));
	}	
	
	public function getName()
	{
		return 'medicalCenter';
	}
}