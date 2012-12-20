<?php
namespace HealthCareAbroad\InstitutionBundle\Form\ListType;

use HealthCareAbroad\InstitutionBundle\Form\Transformer\SpecializationTransformer;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use HealthCareAbroad\TreatmentBundle\Entity\Specialization;

class SpecializationListType extends AbstractType 
{
	
	protected  $doctrine;
	
	function setDoctrine($doctrine)
	{
		$this->doctrine = $doctrine;
	}
	
	public function buildForm(FormBuilderInterface $builder, array $options)
	{

		$transformer = new SpecializationTransformer($this->doctrine->getEntityManager());
		$builder->prependNormTransformer($transformer);
	}
   
    public function getParent()
    {
        return 'text';
    }

    public function getName()
    {
        return 'specializations_autocomplete';
    }
}