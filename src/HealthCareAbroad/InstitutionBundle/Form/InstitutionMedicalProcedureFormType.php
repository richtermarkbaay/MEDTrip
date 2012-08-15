<?php
namespace HealthCareAbroad\InstitutionBundle\Form;

use Symfony\Component\Validator\Constraints\NotBlank;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalProcedure;
use HealthCareAbroad\InstitutionBundle\Form\ListType\InstitutionMedicalCenterListType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;


class InstitutionMedicalProcedureFormType extends AbstractType 
{	
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    	$status = array(
    		InstitutionMedicalProcedure::STATUS_ACTIVE => 'active',
    		InstitutionMedicalProcedure::STATUS_INACTIVE => 'inactive'
    	);

        $builder
			->add('medical_procedure', 'choice', array('label'=>'Procedure', 'required'=>true, 'constraints'=>array(new NotBlank())))
			->add('description', 'textarea')
			->add('status', 'choice', array('choices' => $status));
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalProcedure',
        ));
    }
    
    public function getName()
    {
        return 'institutionMedicalProcedureForm';
    }

}