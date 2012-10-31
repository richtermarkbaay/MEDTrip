<?php
namespace HealthCareAbroad\TreatmentBundle\Form;

use Symfony\Component\Validator\Constraints\NotBlank;

use HealthCareAbroad\TreatmentBundle\Entity\Treatment;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


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
			$institutionTreatmentRepo = $this->doctrine->getRepository('InstitutionBundle:Institution');
			$hasInstitutionTreatment = $institutionTreatmentRepo->getCountByTreatmentId($treatment->getId());

		    if ($hasInstitutionTreatment) {
		        $builder->add('subSpecialization', 'hidden', array('virtual' => 'true', 'label' => 'Sub Specialization', 'read_only' => true));
		    }
		    else {
		        $builder->add('subSpecialization', 'subSpecialization_list');
		    }
		}
		else {
		    $builder->add('subSpecialization', 'subSpecialization_list');
		}

		$builder->add('name');
		$builder->add('status', 'choice', array('choices' => $status));
	}

	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
	    $resolver->setDefaults(array(
			'data_class' => 'HealthCareAbroad\TreatmentBundle\Entity\Treatment'
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