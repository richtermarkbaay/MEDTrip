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
		$medicalProcedure = $options['data'];

		$status = array(
			MedicalProcedure::STATUS_ACTIVE => 'active',
			MedicalProcedure::STATUS_INACTIVE => 'inactive'
		);

		$builder->add('name');
		
		if ($medicalProcedure->getId()) {
		    // we don't allow changing of medicalProcedureType if this has been linked already to institutionMedicalProcedures
		    if (count($medicalProcedure->getInstitutionMedicalProcedures())) {
		        $builder->add('medicalProcedureType', 'hidden', array('virtual' => 'true', 'label' => 'Procedure Type', 'read_only' => true));
		    }
		    else {
		        $builder->add('medicalProcedureType', 'medicalproceduretype_list');
		    }
		}
		else {
		    $builder->add('medicalProcedureType', 'medicalproceduretype_list');
		}

// 		if((!$medicalProcedure->getId() && $medicalProcedure->getMedicalProcedureType()) || count($medicalProcedure->getInstitutionMedicalProcedures()) )
// 			$builder->add('medicalProcedureType', 'hidden', array('property_path' => 'medicalProcedureType.id', 'label' => 'Procedure Type'));
// 		else
// 			$builder->add('medicalProcedureType', 'medicalproceduretype_list');
		

		$builder->add('status', 'choice', array('choices' => $status));
	}

	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
	    $resolver->setDefaults(array(
			'data_class' => 'HealthCareAbroad\MedicalProcedureBundle\Entity\MedicalProcedure'
		));
	}

	public function getName()
	{
		return 'medicalProcedure';
	}
}
