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
	
	public function __construct(Institution $institution=null) {
		$this->institution = $institution;
	}
	
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
    	$institution = $this->institution;
        $resolver->setDefaults(array(
            	'label' => 'Treatments',
        		'class' => 'HealthCareAbroad\TreatmentBundle\Entity\Treatment',
                'query_builder' => function(EntityRepository $er) use ($institution) {
                // $er is a HealthCareAbroad\MedicalProcedureBundle\Repository\MedicalCenterRepository 
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
        return 'treatment_list';
    }
}