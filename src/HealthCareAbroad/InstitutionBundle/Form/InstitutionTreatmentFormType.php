<?php
namespace HealthCareAbroad\InstitutionBundle\Form;

use Symfony\Component\Validator\Constraints\NotBlank;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionTreatmentProcedure;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class InstitutionTreatmentFormType extends AbstractType
{
	protected $doctrine;

	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$treatmentProcedure = $options['data'];

		    $builder->add('treatment', 'subSpecialization_list', array('expanded' => true,'multiple' => true));
		

// 		$builder->add('name');
// 		$builder->add('status', 'choice', array('choices' => $status));
	}

// 	public function setDefaultOptions(OptionsResolverInterface $resolver)
// 	{
// 	    $resolver->setDefaults(array(
// 			'data_class' => 'HealthCareAbroad\MedicalProcedureBundle\Entity\TreatmentProcedure'
// 		));
// 	}

	public function getName()
	{
		return 'treatments';
	}
	
	function setDoctrine($doctrine) {
		$this->doctrine = $doctrine;
	}
}