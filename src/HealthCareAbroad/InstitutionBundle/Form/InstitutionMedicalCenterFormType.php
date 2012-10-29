<?php
namespace HealthCareAbroad\InstitutionBundle\Form;

use Symfony\Component\Validator\Constraints\NotBlank;

use Doctrine\ORM\EntityRepository;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\AbstractType;

class InstitutionMedicalCenterFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $institutionMedicalCenter = $options['data'];
        
        if (!$institutionMedicalCenter instanceof InstitutionMedicalCenter) {
            throw new \Exception('Expected InstitutionMedicalCenter as data.');
        }
        
        if (!$institutionMedicalCenter->getId()) {
            $builder->add('medicalCenter', 'medicalCenter_list', array(
                'label' => 'Specialization',
                'query_builder' => function(EntityRepository $er){
                    return $er->getQueryBuilderForActiveMedicalCenters();
                }
            ));
        }
        
//         $builder->add('treatmentProcedures', 'entity', array(
//             'label' => 'Treatments',
//             'class' => 'HealthCareAbroad\MedicalProcedureBundle\Entity\TreatmentProcedure',
//             'multiple' => true,
//             'attr' => array('class' => 'institutionTreatmentProcedures')
//         ));
        
        $builder->add('description', 'textarea', array(
            'label' => 'Specialization Details',
            'constraints' => array(new NotBlank()),
            'attr' => array('class'=>'tinymce')
        ));
    }
    
    public function getName(){
        return 'institutionMedicalCenter';
    }
}