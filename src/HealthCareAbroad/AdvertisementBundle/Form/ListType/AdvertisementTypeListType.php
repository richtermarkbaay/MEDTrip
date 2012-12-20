<?php
namespace HealthCareAbroad\AdvertisementBundle\Form\ListType;

use HealthCareAbroad\AdvertisementBundle\Entity\AdvertisementType;

use Symfony\Component\Form\FormBuilderInterface;

use Doctrine\ORM\EntityRepository;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class AdvertisementTypeListType extends AbstractType 
{

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'label' => 'Advertisement Type',
            'property' => 'name',
            'virtual' => true,
			'class' => 'HealthCareAbroad\AdvertisementBundle\Entity\AdvertisementType',
            'query_builder' => function(EntityRepository $er) {
                return $er->createQueryBuilder('a')->add('where', 'a.status = :status')
                          ->setParameter('status', AdvertisementType::STATUS_ACTIVE)
                          ->orderBy('a.id', 'ASC');
            }
        ));
    }

    public function getParent()
    {
        return 'entity';
    }

    public function getName()
    {
        return 'advertisementType_list';
    }
}