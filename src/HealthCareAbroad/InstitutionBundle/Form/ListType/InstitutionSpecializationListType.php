<?php
namespace HealthCareAbroad\InstitutionBundle\Form\ListType;

use HealthCareAbroad\InstitutionBundle\Repository\InstitutionSpecializationRepository;

use HealthCareAbroad\InstitutionBundle\Entity\Institution;

use Symfony\Component\Form\FormInterface;

use Symfony\Component\Form\FormView;

use Doctrine\ORM\EntityRepository;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;


class InstitutionSpecializationListType extends AbstractType
{
    const SHOW_SELECTED = 1;
    const SHOW_UNSELECTED = 2;
    
    private $serviceContainer;

     private $institution;
     private $filter;

    public function __construct($institution=null, $filter = self::SHOW_SELECTED) {
        $this->filter = $filter;
        $this->institution = $institution;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $filter = $this->filter;
        $institution = $this->institution;
        $params = array(
            'filter' => $this->filter,
            'institution' => $this->institution,
            'selected' => self::SHOW_SELECTED,
            'unselected' => self::SHOW_UNSELECTED
        );

        $resolver->setDefaults(array(
            'virtual' => true,
            'empty_value' => '<-- select specialization -->',
            'class' => 'HealthCareAbroad\TreatmentBundle\Entity\Specialization',
            'query_builder' => function(EntityRepository $er) use ($params) {
                
                return $er->getQueryBuilderForActiveSpecializations();
//                 $qb = $er->createQueryBuilder('a');
                
//                 $qb1 = $qb->getEntityManager()->createQueryBuilder();
//                 $qb1->select('d.id')
//                     ->from('InstitutionBundle:InstitutionSpecialization', 'b')
//                     ->leftJoin('b.institutionMedicalCenter', 'c')
//                     ->leftJoin('b.specialization', 'd')
//                     ->where('c.institution = :institution')
//                     ->groupBy('b.specialization');

//                 if($params['filter'] == $params['unselected']) {
//                     $qb->where($qb->expr()->notIn('a.id', $qb1->getDQL()));

//                 } else if($params['filter'] == $params['selected']) {
//                     $qb->where($qb->expr()->in('a.id', $qb1->getDQL()));                    
//                 }                

//                 $qb->setParameter('institution', $params['institution']->getId());

//                 return $qb;
            }
        ));
    }

    public function getParent()
    {
        return 'entity';
    }

    public function getName()
    {
        return 'institutionSpecialization_list';
    }
}