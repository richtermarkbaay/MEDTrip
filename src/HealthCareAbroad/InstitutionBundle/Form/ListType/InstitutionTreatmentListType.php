<?php
/*
 * 
 * @author Chaztine Blance
 */
namespace HealthCareAbroad\InstitutionBundle\Form\ListType;
use HealthCareAbroad\InstitutionBundle\Entity\Institution;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class InstitutionTreatmentListType extends AbstractType
{
	private $institution;
	private $institutionService;
	
	public function __construct($params) {
	    foreach($params as $key => $val) {
	        $this->{$key} = $value;	        
	    }
	}

	public function setInstitutionService($service)
	{
	    $this->institutionService = $service;
	}
	
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        var_dump($this->institutionService); exit;
        
        $qb = $this->institutionService->getTreatmentQueryBuilderByInstitution($this->institution);
        
    	$institution = $this->institution;
        $resolver->setDefaults(array(
            	'label' => 'Treatments',
        		'class' => 'HealthCareAbroad\TreatmentBundle\Entity\Treatment',
                'query_builder' => function(EntityRepository $er) use ($institution) {

                    //$qb = $er->createQueryBuilder();
                    
                    $qb1 = $qb->getEntityManager()->createQueryBuilder();
                    $qb1->select('d.treatments')
                    ->from('InstitutionBundle:InstitutionSpecialization', 'b')
                    ->leftJoin('b.institutionMedicalCenter', 'c')
                    ->leftJoin('b.specialization', 'd')
                    ->where('c.institution = :institution')
                    ->groupBy('b.specialization');
                    
                    if($params['filter'] == $params['unselected']) {
                        $qb->where($qb->expr()->notIn('a.id', $qb1->getDQL()));
                    
                    } else if($params['filter'] == $params['selected']) {
                        $qb->where($qb->expr()->in('a.id', $qb1->getDQL()));
                    }
                    
                    $qb->setParameter('institution', $params['institution']->getId());
                    
                    return $qb;
                    
                return $er->getQueryBuilderForActiveTreatmentProceduresByMedicalCenter();
        	}
        ));
    }
    
    public function getParent()
    {
        return 'entity';
    }
    
    public function getName()
    {
        return 'institutionTreatment_list';
    }
}