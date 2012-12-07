<?php
namespace HealthCareAbroad\AdvertisementBundle\Form\ListType;

use Symfony\Component\Form\FormBuilderInterface;

use Doctrine\ORM\EntityRepository;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class AdvertisementPropertyNameListType extends AbstractType 
{

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'label' => 'Optional Properties: ',
            'property' => 'label',
            'multiple' => true,
            'expanded' => true,
			'class' => 'HealthCareAbroad\AdvertisementBundle\Entity\AdvertisementPropertyName'
        ));
    }

    public function getParent()
    {
        return 'entity';
    }

    public function getName()
    {
        return 'advertisementPropertyName_list';
    }
}