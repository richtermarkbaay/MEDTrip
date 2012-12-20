<?php
namespace HealthCareAbroad\InstitutionBundle\Form\ListType;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenterStatus;
use HealthCareAbroad\InstitutionBundle\Entity\Institution;
use Doctrine\ORM\EntityRepository;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class InstitutionMedicalCenterListType extends AbstractType 
{	
	private $institution;
	
	public function __construct(Institution $institution)
	{
	    $this->institution = $institution;
	}
	
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {	
        $institution = $this->institution;

        $resolver->setDefaults(array(
        	'property' => 'name',
			'class' => 'HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter',
			'query_builder' => function(EntityRepository $er) use ($institution) {
                $qb = $er->createQueryBuilder('a');

                $qb->where('a.institution = :institution');
                $qb->andWhere('a.status = :status');
                $qb->setParameter('status', InstitutionMedicalCenterStatus::APPROVED);
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
        return 'institutionMedicalCenter_list';
    }
}