<?php
namespace HealthCareAbroad\MedicalProcedureBundle\Form;

use HealthCareAbroad\MedicalProcedureBundle\Entity\MedicalProcedureType;

use Doctrine\ORM\EntityManager;

use HealthCareAbroad\MedicalProcedureBundle\Form\DataTransformer\MedicalCentersTransformer;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


class MedicalProcedureTypeType extends AbstractType
{
	private $em;
	private $id;

	public function __construct(EntityManager $em, $id = '')
	{
		$this->em = $em;
		$this->id = $id;
	}

	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$transformer = new MedicalCentersTransformer($this->em);
		$builder->add('id', 'hidden', array('virtual'=>true, 'attr' => array('value' => $this->id)));
		$builder->add('name', 'text');
		$builder->add('description', 'textarea');
		$builder->add($builder->create('medical_center','textarea', array('attr'=>array('class'=>'center-autocomplete')))->addModelTransformer($transformer));
		$builder->add('status', 'choice', array('choices' => array_keys(MedicalProcedureType::$STATUS)));
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
