<?php
namespace HealthCareAbroad\InstitutionBundle\Form;

use Symfony\Component\Validator\Constraints\NotBlank;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalProcedure;
use HealthCareAbroad\InstitutionBundle\Form\ListType\InstitutionMedicalCenterListType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;


class InstitutionMedicalProcedureType extends AbstractType 
{	
	private $institutionId;

	public function __construct($institutionId)
	{
		$this->institutionId = $institutionId;	
	}
	
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
			->add('medical_center', new InstitutionMedicalCenterListType($this->institutionId), array('constraints'=>array(new NotBlank())))
			->add('procedure_type', 'choice', array('virtual' =>true, 'required'=>true, 'constraints'=>array(new NotBlank())))
			->add('medical_procedure', 'choice', array('label'=>'Procedure', 'required'=>true, 'constraints'=>array(new NotBlank())))
			->add('description', 'textarea')
			->add('status', 'choice', array('choices' => array_flip(InstitutionMedicalProcedure::$STATUS)));
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalProcedure',
        ));
    }
    
    public function getName()
    {
        return 'institutionMedicalProcedure';
    }

}