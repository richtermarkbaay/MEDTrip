<?php
namespace HealthCareAbroad\InstitutionBundle\Form;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\AbstractType;

class InstitutionMedicalCenterGroupFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name','text', array('label' => 'Medical Center Name'))
        ->add('description', 'textarea', array('label' => 'Medical Center Short Description','attr' => array('class' => 'tinymce')));
        
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenterGroup'
        ));
    }
    
    public function getName()
    {
        return 'institutionMedicalCenterGroup';
    }
}