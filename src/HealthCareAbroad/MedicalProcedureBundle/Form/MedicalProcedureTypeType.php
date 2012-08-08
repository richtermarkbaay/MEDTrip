<?php
namespace HealthCareAbroad\MedicalProcedureBundle\Form;

use Symfony\Component\Validator\Constraints\NotBlank;

use HealthCareAbroad\MedicalProcedureBundle\Entity\MedicalProcedureType as MedicalProcedureTypeEntity;

use Doctrine\ORM\EntityManager;

use HealthCareAbroad\MedicalProcedureBundle\Form\DataTransformer\MedicalCentersTransformer;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


class MedicalProcedureTypeType extends AbstractType
{
	private $em;

	public function __construct(EntityManager $em)
	{
		$this->em = $em;
	}

	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$commonConstraints = array(new NotBlank());

		$transformer = new MedicalCentersTransformer($this->em);
		$builder->add('name', 'text', array('constraints'=> $commonConstraints));
		$builder->add('description', 'textarea', array('constraints'=> $commonConstraints));
		$builder->add($builder->create('medical_center','textarea', array('attr'=>array('class'=>'autocomplete-medical-center')))->addModelTransformer($transformer));
		$builder->add('status', 'choice', array('choices' => array_flip(MedicalProcedureTypeEntity::$STATUS)));
	}

	// How does it work?
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
