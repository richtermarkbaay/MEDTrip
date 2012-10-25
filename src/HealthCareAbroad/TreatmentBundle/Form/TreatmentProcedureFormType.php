<?php
namespace HealthCareAbroad\TreatmentBundle\Form;

use Symfony\Component\Validator\Constraints\NotBlank;

use HealthCareAbroad\TreatmentBundle\Entity\TreatmentProcedure;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


class TreatmentProcedureFormType extends AbstractType
{
	protected $doctrine;

	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$treatmentProcedure = $options['data'];

		$status = array(
			TreatmentProcedure::STATUS_ACTIVE => 'active',
			TreatmentProcedure::STATUS_INACTIVE => 'inactive'
		);
		
		if ($treatmentProcedure->getId()) {
			$institutionTreatmentProcedureRepo = $this->doctrine->getRepository('InstitutionBundle:InstitutionTreatmentProcedure');
			$hasInstitutionTreatmentProcedure = $institutionTreatmentProcedureRepo->getCountByMedicalProcedureId($treatmentProcedure->getId());

		    if ($hasInstitutionTreatmentProcedure) {
		        $builder->add('treatment', 'hidden', array('virtual' => 'true', 'label' => 'Procedure Type', 'read_only' => true));
		    }
		    else {
		        $builder->add('treatment', 'medicalproceduretype_list');
		    }
		}
		else {
		    $builder->add('treatment', 'medicalproceduretype_list');
		}

		$builder->add('name');
		$builder->add('status', 'choice', array('choices' => $status));
	}

	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
	    $resolver->setDefaults(array(
			'data_class' => 'HealthCareAbroad\TreatmentBundle\Entity\TreatmentProcedure'
		));
	}

	public function getName()
	{
		return 'treatmentProcedure';
	}
	
	function setDoctrine($doctrine) {
		$this->doctrine = $doctrine;
	}
}
