<?php
namespace HealthCareAbroad\TreatmentBundle\Form\ListType;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class SpecializationListType extends AbstractType
{
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'property' => 'name',
            'label' => 'Specialization',
            'class' => 'HealthCareAbroad\TreatmentBundle\Entity\Specialization',
            'query_builder' => function(EntityRepository $er) { return $er->getQueryBuilderForActiveSpecializations(); }
        ));
    }

    public function getParent()
    {
        return 'entity';
    }

    public function getName()
    {
        return 'specialization_list';
    }
}