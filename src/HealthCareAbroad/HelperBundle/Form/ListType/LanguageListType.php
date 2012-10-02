<?php
namespace HealthCareAbroad\HelperBundle\Form\ListType;

use HealthCareAbroad\InstitutionBundle\Form\Transformer\LanguageTransformer;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use HealthCareAbroad\AdminBundle\Entity\Language;

class LanguageListType extends AbstractType 
{
	
	protected  $doctrine;
	
	function setDoctrine($doctrine)
	{
		$this->doctrine = $doctrine;
	}
	
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$transformer = new LanguageTransformer($this->doctrine->getEntityManager());
		$builder->prependNormTransformer($transformer);
	}
   
    public function getParent()
    {
        return 'hidden';
    }

    public function getName()
    {
        return 'language_autocomplete';
    }
}