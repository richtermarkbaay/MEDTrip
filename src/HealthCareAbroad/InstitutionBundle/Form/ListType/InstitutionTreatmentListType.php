<?php
/**
 * 
 * @author Adelbert D. Silla
 *
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

	public function __construct(Institution $institution) {
	    $this->institution = $institution;
	}
	
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
    	$choices = array();

    	foreach($this->institution->getInstitutionMedicalCenters() as $center) {

    	    foreach($center->getInstitutionSpecializations() as $institutionSpecialization) {

    	        foreach($institutionSpecialization->getTreatments() as $treatment) {
    	            $choices[$institutionSpecialization->getSpecialization()->getName()][$treatment->getId()] = $treatment->getName();
    	        }
    	    }
    	}

        $resolver->setDefaults(array(
        	'label' => 'Treatments',
            'choices' => $choices
        ));
    }
    
    public function getParent()
    {
        return 'choice';
    }
    
    public function getName()
    {
        return 'institutionTreatment_list';
    }
}