<?php
namespace HealthCareAbroad\InstitutionBundle\Form;

use Symfony\Component\Form\Util\PropertyPath;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\AbstractType;

class InstitutionMedicalCenterBusinessHourFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('businessHours','hidden');
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter'
        ));

    }

    public function getName()
    {
        return 'institutionMedicalCenterBusinessHour';
    }
}