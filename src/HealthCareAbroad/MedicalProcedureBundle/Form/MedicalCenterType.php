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
	protected $doctrine;
	
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$medicalCenter = $options['data'];

		$status = array(
			MedicalCenter::STATUS_ACTIVE => 'active',
			MedicalCenter::STATUS_INACTIVE => 'inactive'
		);

		$institutionMedicalCenterRepo = $this->doctrine->getRepository('InstitutionBundle:InstitutionMedicalCenter');
		$hasInstitutionMedicalCenter = $institutionMedicalCenterRepo->getCountByMedicalCenterId($medicalCenter->getId());

		if($medicalCenter->getId() && $hasInstitutionMedicalCenter)
			$builder->add('name', 'text', array('virtual' => true, 'read_only' => true));
		else 
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
	
	public function setDoctrine($doctrine)
	{
		$this->doctrine = $doctrine;
	}
}