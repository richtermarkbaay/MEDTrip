<?php
namespace HealthCareAbroad\TreatmentBundle\Form;

use Gaufrette\Filesystem;

use HealthCareAbroad\TreatmentBundle\Services\SpecializationMediaService;

use HealthCareAbroad\MediaBundle\Form\AdminMediaFileType;
use HealthCareAbroad\TreatmentBundle\Entity\Specialization;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use HealthCareAbroad\TreatmentBundle\Form\DataTransformer\SpecializationStatusToBooleanTransformer;

class SpecializationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $specialization = $options['data'];

        $status = array(
            Specialization::STATUS_ACTIVE => 'active',
            Specialization::STATUS_INACTIVE => 'inactive'
        );

        $builder->add('name');
        $builder->add('description');
        $builder->add('media', new AdminMediaFileType($specialization->getMedia()));
        $builder->add('status', 'choice', array('choices' => $status));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'HealthCareAbroad\TreatmentBundle\Entity\Specialization'
        ));
    }

    public function getName()
    {
        return 'specialization_form';
    }
}