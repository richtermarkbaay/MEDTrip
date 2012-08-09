<?php
namespace HealthCareAbroad\HelperBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use HealthCareAbroad\HelperBundle\Form\DataTransformer\CityListTransformer;

class CityType extends AbstractType
{
	private $id;

	public function __construct($countryId)
	{
		$this->countryId = $countryId;		
	}
	
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		
		$entityManager = $options['em'];
		$transformer = new IssueToNumberTransformer($entityManager);
		$builder->add('city', 'entity')->prependNormTransformer($transformer);
		
		
	}

	// How does it work?
	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
	    $resolver->setDefaults(array(
			'data_class' => 'HealthCareAbroad\HelperBundle\Entity\Tag',
		));
	}

	public function getName()
	{
		return 'tag';
	}
}
