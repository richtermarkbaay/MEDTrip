<?php
namespace HealthCareAbroad\InstitutionBundle\Form;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\AbstractType;

class InstitutionMedicalCenterFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name','text', array('label' => 'Name'))
        ->add('description', 'textarea', array('label' => 'Details','attr' => array('class' => 'tinymce')));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter'
        ));

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

    public function getName()
    {
        return 'institutionMedicalCenter';
    }
}