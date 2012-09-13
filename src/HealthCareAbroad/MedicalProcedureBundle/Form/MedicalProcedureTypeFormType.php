<?php
namespace HealthCareAbroad\MedicalProcedureBundle\Form;

use HealthCareAbroad\MedicalProcedureBundle\Entity\MedicalProcedureType;
use HealthCareAbroad\MedicalProcedureBundle\Repository\MedicalProcedureRepository;
use HealthCareAbroad\MedicalProcedureBundle\Entity\MedicalProcedureType as MedicalProcedureTypeEntity;

use Doctrine\ORM\EntityManager;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class MedicalProcedureTypeFormType extends AbstractType
{
	protected $doctrine;
	
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$medicalProcedureType = $options['data'];

		$status = array(
			MedicalProcedureType::STATUS_ACTIVE => 'active',
			MedicalProcedureType::STATUS_INACTIVE => 'inactive'
		);

		if ($medicalProcedureType->getId()) {
			$medicalProcedureRepo = $this->doctrine->getRepository('MedicalProcedureBundle:MedicalProcedure');
 			$hasMedicalProcedureType = $medicalProcedureRepo->getCountByMedicalProcedureTypeId($medicalProcedureType->getId());

 			$institutionMedicalProcedureTypeRepo = $this->doctrine->getRepository('InstitutionBundle:InstitutionMedicalProcedureType');
 			$hasInstitutionMedicalProcedureType = $institutionMedicalProcedureTypeRepo->getCountByMedicalProcedureTypeId($medicalProcedureType->getId());

			if($hasInstitutionMedicalProcedureType || $hasMedicalProcedureType) {
		        $builder->add('medicalCenter', 'hidden', array('virtual' => true, 'label' => 'Medical Center', 'read_only' => true));
			}
			else {
				$builder->add('medicalCenter', 'medicalCenter_list');
			}
		}
		else {
		    $builder->add('medicalCenter', 'medicalCenter_list');
		}

		$builder->add('name');
		$builder->add('description', 'textarea');
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
	
	function setDoctrine($doctrine) {
		$this->doctrine = $doctrine;
	}
}
