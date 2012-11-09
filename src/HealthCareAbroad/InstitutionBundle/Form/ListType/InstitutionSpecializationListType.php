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
     private $serviceContainer;

     private $institution;

    public function __construct($institution=null) {
        $this->institution = $institution;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $institution = $this->institution;
        $resolver->setDefaults(array(
            'virtual' => true,
            'empty_value' => '<-- select specialization -->',
            'class' => 'HealthCareAbroad\TreatmentBundle\Entity\Specialization',
            'query_builder' => function(EntityRepository $er) use ($institution) {
                $qb = $er->createQueryBuilder('a');

                $qb1 = $qb->getEntityManager()->createQueryBuilder();
                $qb1->select('d.id')
                    ->from('InstitutionBundle:InstitutionSpecialization', 'b')
                    ->leftJoin('b.institutionMedicalCenter', 'c')
                    ->leftJoin('b.specialization', 'd')
                    ->where('c.institution = :institution')
                    ->groupBy('b.specialization');

                $qb->where($qb->expr()->notIn('a.id', $qb1->getDQL()));

                $qb->setParameter('institution', $institution->getId());

                return $qb;
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