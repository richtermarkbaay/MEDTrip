<?php 
namespace HealthCareAbroad\TreatmentBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use HealthCareAbroad\TreatmentBundle\Form\DataTransformer\SpecializationStatusToBooleanTransformer;

class SpecializationStatusSelectorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(new SpecializationStatusToBooleanTransformer());
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'invalid_message' => 'The selected status does not exist',
        	'label' => 'Activate?'
		));
    }

    public function getParent()
    {
        return 'checkbox';
    }

    public function getName()
    {
        return 'medicalCenterStatusSelector';
    }
}