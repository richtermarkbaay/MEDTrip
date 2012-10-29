<?php
namespace HealthCareAbroad\TreatmentBundle\Form\ListType;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class SubSpecializationListType extends AbstractType
{

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'property' => 'name',
            'label' => 'Sub Specialization',
            'class' => 'HealthCareAbroad\TreatmentBundle\Entity\SubSpecialization',
// 			'query_builder' => function(EntityRepository $er) {
// 			    return $er->getQueryBuilderForGettingAvailableTreatments();
//             }
        ));
    }

    public function getParent()
    {
        return 'entity';
    }

    public function getName()
    {
        return 'subSpecialization_list';
    }
}