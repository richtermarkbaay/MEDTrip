<?php
namespace HealthCareAbroad\MedicalProcedureBundle\Form;

use HealthCareAbroad\MedicalProcedureBundle\Entity\Treatment;
use HealthCareAbroad\MedicalProcedureBundle\Repository\MedicalProcedureRepository;
use HealthCareAbroad\MedicalProcedureBundle\Entity\Treatment as TreatmentEntity;

use Doctrine\ORM\EntityManager;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class TreatmentFormType extends AbstractType
{
	protected $doctrine;
	
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$treatment = $options['data'];

		$status = array(
			Treatment::STATUS_ACTIVE => 'active',
			Treatment::STATUS_INACTIVE => 'inactive'
		);

		if ($treatment->getId()) {
			$treatmentProcedureRepo = $this->doctrine->getRepository('MedicalProcedureBundle:TreatmentProcedure');
 			$hasTreatment = $treatmentProcedureRepo->getCountByTreatmentId($treatment->getId());

//  			$institutionTreatmentRepo = $this->doctrine->getRepository('InstitutionBundle:InstitutionTreatment');
//  			$hasInstitutionTreatment = $institutionTreatmentRepo->getCountByTreatmentId($treatment->getId());

 			//if($hasInstitutionTreatment || $hasTreatment) {
			if($hasTreatment) {
		        $builder->add('medicalCenter', 'hidden', array('virtual' => true, 'label' => 'Specialization', 'read_only' => true));
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
			'data_class' => 'HealthCareAbroad\MedicalProcedureBundle\Entity\Treatment',
		));
	}

	public function getName()
	{
		return 'treatment';
	}
	
	function setDoctrine($doctrine) {
		$this->doctrine = $doctrine;
	}
}
