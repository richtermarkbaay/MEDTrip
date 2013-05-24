<?php
namespace HealthCareAbroad\InstitutionBundle\Form\ListType;

use HealthCareAbroad\InstitutionBundle\Form\Transformer\MedicalProviderGroupTransformer;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use HealthCareAbroad\InstitutionBundle\Entity\MedicalProviderGroup;
use Symfony\Component\Form\FormView;

class MedicalProviderGroupListType extends AbstractType 
{
	protected  $doctrine;
	
	function setDoctrine($doctrine)
	{
		$this->doctrine = $doctrine;
	}
	
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
	    $builder->addModelTransformer(new MedicalProviderGroupTransformer($this->doctrine->getEntityManager()));
	}
	
	public function getName()
	{
	    return 'medicalProviderGroup_list';
	}
	
	public function getParent()
	{
	    return 'text';
	}
}