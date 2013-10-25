<?php
namespace HealthCareAbroad\HelperBundle\Form\ListType;

use Doctrine\ORM\EntityRepository;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;
use HealthCareAbroad\HelperBundle\Services\AncillaryServicesService;

class DoctorSpecializationsListType extends AbstractType
{
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'property' => 'name',
            'label' => 'Specialization',
            'class' => 'HealthCareAbroad\TreatmentBundle\Entity\Specialization',
            'query_builder' => function(EntityRepository $er) { return $er->getQueryBuilderForDocSpecializationsWithSpecialities(); }
        ));
    }

    public function getParent()
    {
        return 'entity';
        //return 'choice';
    }

    public function getName()
    {
        return 'doctorSpecializations_list';
    }
}