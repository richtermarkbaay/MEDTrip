<?php
namespace HealthCareAbroad\InstitutionBundle\Form\ListType;

use HealthCareAbroad\InstitutionBundle\Entity\Institution;

use Symfony\Component\Form\FormInterface;

use Symfony\Component\Form\FormView;

use Doctrine\ORM\EntityRepository;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;


class InstitutionMedicalCenterListType extends AbstractType 
{	
 	private $serviceContainer;
 	
 	private $institution;

	public function __construct(Institution $institution) {
		$this->institution = $institution;
	}
	
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $institution = $this->institution;
        $resolver->setDefaults(array(
        	'virtual' => true,
            'empty_value' => '<-- select center -->',
        	'property' => 'name',
			'class' => 'HealthCareAbroad\MedicalProcedureBundle\Entity\MedicalCenter',
            'query_builder' => function(EntityRepository $er) use ($institution) {
                // $er is a HealthCareAbroad\MedicalProcedureBundle\Repository\MedicalCenterRepository 
                return $er->getCreateBuilderForMedicalCentersOfInstitution($institution); 
//         		return $er->createQueryBuilder('c')
//         			->add('where', 'c.institution = :institution')
//         			->setParameter('institution', $institutionId)
//         			->orderBy('c.medicalCenter', 'ASC');
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