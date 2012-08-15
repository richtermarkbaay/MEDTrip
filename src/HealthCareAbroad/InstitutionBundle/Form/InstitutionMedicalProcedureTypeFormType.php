<?php

namespace HealthCareAbroad\InstitutionBundle\Form;

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
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setRequired(array('institution'));
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $institution = $options['institution'];        
        $medicalCenterId = \array_key_exists('medicalCenterId', $options['data']) && $options['data']['medicalCenterId'] ? $options['data']['medicalCenterId'] : 0;
        
        if (!$medicalCenterId) {
            $builder->add('medicalCenter', new  InstitutionMedicalCenterListType($institution), array('label' => 'Medical Center:'));
            //$builder->add('medicalCenter', 'institutionMedicalCenter_list', array('institutionId' => $institutionId, 'label' => 'Medical Center:'));
        }
        else {
            $builder->add('medicalCenter', 'hidden', array('label' => 'Medical Center:', 'value' => $medicalCenterId));
        }
        
        $builder->add('medicalProcedureType', 'medicalproceduretype_list', array('label' => 'Procedure Type:', 'constraints' => new NotBlank()));
        $builder->add('description', 'textarea', array('label' => 'Description:', 'constraints' => new NotBlank()));
        
    }
    
    public function getName()
    {
        return 'institutionMedicalProcedureTypeForm';
    }
}