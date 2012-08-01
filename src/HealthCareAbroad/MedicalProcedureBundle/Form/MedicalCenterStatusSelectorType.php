<?php 
namespace HealthCareAbroad\MedicalProcedureBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use HealthCareAbroad\MedicalProcedureBundle\Form\DataTransformer\MedicalCenterStatusToBooleanTransformer;

class MedicalCenterStatusSelectorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(new MedicalCenterStatusToBooleanTransformer());
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