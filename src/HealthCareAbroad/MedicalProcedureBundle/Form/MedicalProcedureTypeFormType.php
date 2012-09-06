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
		$medicalProcedureType = $options['data'];

		$status = array(
			MedicalProcedureType::STATUS_ACTIVE => 'active',
			MedicalProcedureType::STATUS_INACTIVE => 'inactive'
		);

		$builder->add('name');
		$builder->add('description');

		if((!$medicalProcedureType->getId() && $medicalProcedureType->getMedicalCenter()) || count($medicalProcedureType->getMedicalProcedures()) ) {
		    // we cannot use property_path medicalCenter.id since it will call MedicalCenter.setId which is not existing, 
		    // also there is no need to put this value since we have a rule that if we reach here [based on the if condition above], we won't allow changing of medical center of already saved MedicalProcedureType 
		    //$builder->add('medicalCenter', 'hidden', array('property_path' => 'medicalCenter.id', 'label' => 'Medical Center'));
		    $builder->add('medicalCenter', 'hidden', array('virtual' => true, 'label' => 'Medical Center'));
		}
		else {
		    $builder->add('medicalCenter', 'medicalCenter_list');
		}

		$builder->add('status', 'choice', array('choices' => $status));
	}

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
