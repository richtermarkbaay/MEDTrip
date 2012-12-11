<?php
namespace HealthCareAbroad\InstitutionBundle\Form\ListType;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;


class InstitutionDoctorListType extends AbstractType
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
            'property' => 'first_name',
            'virtual' => true,
            'empty_value' => '<-- select specialization -->',
            'class' => 'HealthCareAbroad\DoctorBundle\Entity\Doctor',
            'query_builder' => function(EntityRepository $er) use ($institution) {
                $qb = $er->createQueryBuilder('a');
            
                $qb1 = $qb->getEntityManager()->createQueryBuilder();
                $qb1->select('c.id')->from('InstitutionBundle:InstitutionMedicalCenter', 'b')
                    ->leftJoin('b.doctors', 'c')
                    ->where('b.institution = :institution')
                    ->andWhere('c.id IS NOT NULL');

                $qb->where($qb->expr()->in('a.id', $qb1->getDQL()));
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
        return 'institutionDoctor_list';
    }
}