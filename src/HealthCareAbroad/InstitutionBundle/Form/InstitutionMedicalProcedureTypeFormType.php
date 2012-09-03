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

        $builder->add('medicalProcedureType', 'medicalproceduretype_list', array(
            'query_builder' => function(EntityRepository $er) use ($institutionMedicalProcedureType) {
                return $er->getQueryBuilderForAvailableInstitutionMedicalProcedureTypes($institutionMedicalProcedureType->getInstitutionMedicalCenter());
            },
            'virtual' => false, 'label' => 'Procedure Type:', 'constraints' => new NotBlank()));

        $builder->add('description', 'textarea', array('label' => 'Description:', 'constraints' => new NotBlank()));
    }
    
    public function getName()
    {
        return 'institutionMedicalProcedureTypeForm';
    }
}