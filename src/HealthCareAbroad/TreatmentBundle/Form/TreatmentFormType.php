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
			Treatment::STATUS_ACTIVE => 'Active',
			Treatment::STATUS_INACTIVE => 'Inactive'
		);
		
		if ($treatment->getId()) {
			$institutionSpecializationRepo = $this->doctrine->getRepository('InstitutionBundle:InstitutionSpecialization');
			$hasInstitutionTreatment = $institutionSpecializationRepo->getTreatmentCountByTreatmentId($treatment->getId());

		    if ($hasInstitutionTreatment) {
		        $builder->add('specialization', 'hidden', array('virtual' => 'true', 'label' => 'Specialization', 'read_only' => true));
		        //$builder->add('subSpecializations', 'hidden', array('virtual' => 'true', 'label' => 'Sub Specialization', 'read_only' => true));
		    }
		    else {
		        $builder->add('specialization', 'specialization_list');
		        $builder->add('subSpecializations', 'subSpecialization_list', array('multiple' => true, 'attr' => array('style' => 'height:200px;width:300px;')));
		    }
		    $builder->add('subSpecializations', 'subSpecialization_list', array('multiple' => true, 'attr' => array('style' => 'height:200px;width:300px;')));
		}
		else {
		    $builder->add('specialization', 'specialization_list');
		    $builder->add('subSpecializations', 'subSpecialization_list', array('multiple' => true, 'attr' => array('style' => 'height:200px;width:300px;')));
		}

		$builder->add('name');
		$builder->add('description', 'textarea', array(
            'label' => 'Details'
        ));
		$builder->add('treatmentTerms', 'text', array('label' => 'Treatment Terms', 'virtual' => true, 'attr' => array('class' => 'autocompleteTerms')));
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