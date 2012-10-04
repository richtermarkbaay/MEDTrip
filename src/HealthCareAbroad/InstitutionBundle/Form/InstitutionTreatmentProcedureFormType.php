<?php
namespace HealthCareAbroad\InstitutionBundle\Form;

use HealthCareAbroad\MedicalProcedureBundle\Form\ListType\TreatmentProcedureListType;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Validator\Constraints\NotBlank;
use HealthCareAbroad\InstitutionBundle\Entity\InstitutionTreatmentProcedure;
use HealthCareAbroad\InstitutionBundle\Form\ListType\InstitutionMedicalCenterListType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;


class InstitutionTreatmentProcedureFormType extends AbstractType 
{	
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    	$status = array(
    		InstitutionTreatmentProcedure::STATUS_ACTIVE => 'active',
    		InstitutionTreatmentProcedure::STATUS_INACTIVE => 'inactive'
    	);
    	
    	$institutionMedicalProcedure = $options['data'];
    	
    	if (!$institutionMedicalProcedure instanceof InstitutionTreatmentProcedure) {
    	    throw new \Exception(__CLASS__.' expects an instance of HealthCareAbroad\InstitutionBundle\Entity\InstitutionTreatmentProcedure as data');
    	}
    	$institutionTreatment = $institutionMedicalProcedure->getInstitutionTreatment();
    	
    	if ($institutionMedicalProcedure->getId()) {
    	    $builder->add('medicalProcedure', 'hidden', array('virtual' => true));
    	}
    	else {
    	    $builder->add('medicalProcedure', new TreatmentProcedureListType(), array(
                'query_builder' => function (EntityRepository $er) use ($institutionTreatment) {
                    return $er->getQueryBuilderForAvailableInstitutionTreatmentProcedures($institutionTreatment);
        	    },
        	    'label'=>'Procedure',
        	    'required'=>true,
        	    'constraints'=>array(new NotBlank())
    	    ));
    	}
    	
        $builder->add('description', 'textarea', array('constraints' => array(new NotBlank()), 'attr' => array('class' => 'tinymce')))
			->add('status', 'choice', array('choices' => $status));
    }
    
    public function getName()
    {
        return 'institutionMedicalProcedureForm';
    }

}