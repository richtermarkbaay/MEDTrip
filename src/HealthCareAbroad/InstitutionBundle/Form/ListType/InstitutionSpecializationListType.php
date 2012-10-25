<?php
namespace HealthCareAbroad\InstitutionBundle\Form\ListType;

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

	public function __construct(Institution $institution=null) {
		$this->institution = $institution;
	}
	
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $institution = $this->institution;
        $resolver->setDefaults(array(
        	'virtual' => true,
            'empty_value' => '<-- select center -->',
        	'property' => 'name',
			'class' => 'HealthCareAbroad\TreatmentBundle\Entity\Specialization',
            'query_builder' => function(EntityRepository $er) use ($institution) { 
                return $er->getBuilderForSpecializationsOfInstitution($institution);
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