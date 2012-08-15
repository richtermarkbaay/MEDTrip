<?php
namespace HealthCareAbroad\InstitutionBundle\Form;

use HealthCareAbroad\MedicalProcedureBundle\Form\ListType\MedicalProcedureListType;

use Doctrine\ORM\EntityRepository;

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
    	
    	$institutionMedicalProcedureType = $options['institutionMedicalProcedureType'];
    	
    	$builder->add('medicalProcedure', new MedicalProcedureListType(), array(
            'query_builder' => function (EntityRepository $er) use ($institutionMedicalProcedureType) {
    	        return $er->getQueryBuilderForAvailableInstitutionMedicalProcedures($institutionMedicalProcedureType);
            },
            'label'=>'Procedure', 
            'required'=>true, 
            'constraints'=>array(new NotBlank())
        ));
    	
        $builder->add('description', 'textarea')
			->add('status', 'choice', array('choices' => $status));
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setRequired(array('institutionMedicalProcedureType'));
        $resolver->setDefaults(array(
            'data_class' => 'HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalProcedure',
        ));
    }
    
    public function getName()
    {
        return 'institutionMedicalProcedureForm';
    }

}