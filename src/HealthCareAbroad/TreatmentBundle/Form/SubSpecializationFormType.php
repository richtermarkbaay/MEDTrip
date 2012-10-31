<?php
namespace HealthCareAbroad\TreatmentBundle\Form;

use HealthCareAbroad\TreatmentBundle\Entity\SubSpecialization;
use HealthCareAbroad\TreatmentBundle\Entity\Treatment;

use Doctrine\ORM\EntityManager;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class SubSpecializationFormType extends AbstractType
{
    protected $doctrine;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $subSpecialization = $options['data'];

        $status = array(
            SubSpecialization::STATUS_ACTIVE => 'active',
            SubSpecialization::STATUS_INACTIVE => 'inactive'
        );

        if ($subSpecialization->getId()) {
            $treatmentRepo = $this->doctrine->getRepository('TreatmentBundle:Treatment');
             $hasTreatment = $treatmentRepo->getCountBySubSpecializationId($subSpecialization->getId());

             //if($hasInstitutionTreatment || $hasTreatment) {
            if($hasTreatment) {
                $builder->add('specialization', 'hidden', array('virtual' => true, 'label' => 'Specialization', 'read_only' => true));
            }
            else {
                $builder->add('specialization', 'specialization_list');
            }
        }
        else {
                $builder->add('specialization', 'specialization_list');
        }

        $builder->add('name');
        $builder->add('description', 'textarea');
        $builder->add('status', 'choice', array('choices' => $status));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'HealthCareAbroad\TreatmentBundle\Entity\SubSpecialization',
        ));
    }

    public function getName()
    {
        return 'subspecialization';
    }

    function setDoctrine($doctrine) {
        $this->doctrine = $doctrine;
    }
}
