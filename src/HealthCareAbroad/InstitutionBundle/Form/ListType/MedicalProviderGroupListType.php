<?php
namespace HealthCareAbroad\InstitutionBundle\Form\ListType;

use HealthCareAbroad\InstitutionBundle\Form\Transformer\MedicalProviderGroupTransformer;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use HealthCareAbroad\InstitutionBundle\Entity\MedicalProviderGroup;

class MedicalProviderGroupListType extends AbstractType 
{
	
	protected  $doctrine;
	
	function setDoctrine($doctrine)
	{
		$this->doctrine = $doctrine;
	}
	
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$transformer = new MedicalProviderGroupTransformer($this->doctrine->getEntityManager());
		$builder->prependNormTransformer($transformer);
	}
   
    public function getParent()
    {
        return 'text';
    }

    public function getName()
    {
        return 'medicalProviderGroup_autocomplete';
    }
}