<?php

namespace HealthCareAbroad\InstitutionBundle\Form;

use Doctrine\ORM\EntityRepository;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use HealthCareAbroad\MedicalProcedureBundle\Form\ListType\TreatmentListType;

use Symfony\Component\Validator\Constraints\NotBlank;

use HealthCareAbroad\InstitutionBundle\Form\ListType\InstitutionMedicalCenterListType;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionTreatment;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\AbstractType;

use \Exception;

class InstitutionTreatmentFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $institutionTreatment = $options['data'];

        if ($institutionTreatment->getId()) {
            $builder->add('treatment', 'hidden', array('virtual' => true));
        }
        else {
            $builder->add('treatment', 'medicalproceduretype_list', array(
                'query_builder' => function(EntityRepository $er) use ($institutionTreatment) {
                    return $er->getQueryBuilderForAvailableInstitutionTreatments($institutionTreatment->getInstitutionMedicalCenter());
                },
                'virtual' => false, 'label' => 'Treatment:', 'constraints' => new NotBlank()
            ));
        }

        $builder->add('description', 'textarea', array('label' => 'Description:', 'constraints' => new NotBlank(), 'attr' => array('class' => 'tinymce') ));
    }

    public function getName()
    {
        return 'institutionTreatmentForm';
    }
}