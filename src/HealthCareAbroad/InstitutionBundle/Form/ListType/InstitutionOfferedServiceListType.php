<?php
/*
 * 
 * @author Chaztine Blance
 */
namespace HealthCareAbroad\InstitutionBundle\Form\ListType;

use Symfony\Component\Form\FormBuilderInterface;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class InstitutionOfferedServiceListType extends AbstractType
{
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'label' => 'Services Offered',
        	'property' => 'name',
            'class' => 'HealthCareAbroad\AdminBundle\Entity\OfferedService',
			'query_builder' => function(EntityRepository $er){ return $er->createQueryBuilder('u') ->add('where', 'u.status = 1') ->orderBy('u.name', 'ASC');}
        ));
    }
    
    public function getParent()
    {
        return 'entity';
    }
    
    public function getName()
    {
        return 'offeredService_list';
    }
}