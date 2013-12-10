<?php
namespace HealthCareAbroad\HelperBundle\Form\ListType;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CityListType extends AbstractType 
{
    const NAME = 'city_list';

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

    }

	public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'property' => 'name',
            'class' => 'HealthCareAbroad\HelperBundle\Entity\City',
            'query_builder' => function(EntityRepository $er){
                return $er->createQueryBuilder('a') ->orderBy('a.name', 'ASC');
            }
        ));
    }

    public function getParent()
    {
        return 'entity';
    }

    public function getName()
    {
        return self::NAME;
    }
}