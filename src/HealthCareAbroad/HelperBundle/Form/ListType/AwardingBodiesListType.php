<?php
/*
 * 
 * @author Chaztine Blance
 */
namespace HealthCareAbroad\HelperBundle\Form\ListType;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class AwardingBodiesListType extends AbstractType
{
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'label' => 'Awarding Bodies',
        	'property' => 'name',
            'class' => 'HealthCareAbroad\HelperBundle\Entity\AwardingBodies',
			'query_builder' => function(EntityRepository $er){ return $er->createQueryBuilder('u') ->add('where', 'u.status = 1') ->orderBy('u.name', 'ASC');}
        ));
    }
    
    public function getParent()
    {
        return 'entity';
    }
    
    public function getName()
    {
        return 'awarding_bodies_list';
    }
}