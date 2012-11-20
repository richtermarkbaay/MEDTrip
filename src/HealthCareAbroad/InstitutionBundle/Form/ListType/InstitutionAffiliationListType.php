<?php
/*
 * @author Chaztine Blance
 */
namespace HealthCareAbroad\InstitutionBundle\Form\ListType;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class InstitutionAffiliationListType extends AbstractType
{
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'label' => 'Affiliations',
        	'property' => 'name',
            'class' => 'HealthCareAbroad\HelperBundle\Entity\Affiliation',
			'query_builder' => function(EntityRepository $er){ return $er->createQueryBuilder('u') ->add('where', 'u.status = 1') ->orderBy('u.name', 'ASC');}
        ));
    }
    
    public function getParent()
    {
        return 'entity';
    }
    
    public function getName()
    {
        return 'institution_affiliation_list';
    }
}