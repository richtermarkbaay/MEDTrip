<?php
namespace HealthCareAbroad\InstitutionBundle\Form\ListType;

use Doctrine\ORM\EntityRepository;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;


class InstitutionMedicalProcedureTypeListType extends AbstractType 
{	
 	private $institutionId;

	public function __construct($institutionMedicalCenterId) {
		$this->institutionId = $institutionId;
	}
	
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
    	$institutionId = $this->institutionId;
        $resolver->setDefaults(array(
        	'property' => 'medicalCenter.name',
			'class' => 'HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter',
            'query_builder' => function(EntityRepository $er) use ($institutionId) { 
        		return $er->createQueryBuilder('c')
        			->add('where', 'c.institution = :institution')
        			->setParameter('institution', $institutionId)
        			->orderBy('c.medicalCenter', 'ASC');
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