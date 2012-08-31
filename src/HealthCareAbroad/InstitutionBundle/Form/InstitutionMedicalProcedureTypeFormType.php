<?php

namespace HealthCareAbroad\InstitutionBundle\Form;

use Doctrine\ORM\EntityRepository;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use HealthCareAbroad\MedicalProcedureBundle\Form\ListType\MedicalProcedureTypeListType;

use Symfony\Component\Validator\Constraints\NotBlank;

use HealthCareAbroad\InstitutionBundle\Form\ListType\InstitutionMedicalCenterListType;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalProcedureType;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\AbstractType;

use \Exception;

class InstitutionMedicalProcedureTypeFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $institutionMedicalProcedureType = $options['data'];
        $medicalCenter = $institutionMedicalProcedureType->getInstitutionMedicalCenter()->getMedicalCenter();
        
        if ($institutionMedicalProcedureType->getId()) {
            $builder->add('medicalCenter', 'hidden', array('virtual' => true)); // we won't allow editing of Medical Center in edit
            
            // we are in edit mode, so filter the medical procedure types dropdown by adding query builder to limit only the result with the current MedicalProcedureType selected
            $builder->add('medicalProcedureType', 'medicalproceduretype_list', array(
                'query_builder' => function(EntityRepository $er) use ($institutionMedicalProcedureType) {
                    return $er->createQueryBuilder('a')
                        ->select('a')
                        ->where('a.id = :id')
                        ->setParameter('id', $institutionMedicalProcedureType->getMedicalProcedureType()->getId());
                },
                'virtual' => false, 'label' => 'Procedure Type:', 'constraints' => new NotBlank()));
        }
        else {
            $builder->add('medicalCenter', 'medicalCenter_list', array(
                'query_builder' => function(EntityRepository $er) use ($medicalCenter) {
                    return $er->createQueryBuilder('a')->where('a.id =:id')->setParameter('id', $medicalCenter->getId());
                },
                'virtual' => true,'label' => 'Medical Center:'
            ));
            $builder->add('medicalProcedureType', 'medicalproceduretype_list', array('virtual' => false, 'label' => 'Procedure Type:', 'constraints' => new NotBlank()));
        }
        $builder->add('description', 'textarea', array('label' => 'Description:', 'constraints' => new NotBlank()));
        
    }
    
    public function getName()
    {
        return 'institutionMedicalProcedureTypeForm';
    }
}