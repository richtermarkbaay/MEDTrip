<?php
namespace HealthCareAbroad\ListingBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Acme\TaskBundle\Form\DataTransformer\IssueToNumberTransformer;
use Doctrine\Common\Persistence\ObjectManager;

class ProviderSelectorType extends AbstractType
{
	/**
	 * @var ObjectManager
	 */
	private $om;

	/**
	 * @param ObjectManager $om
	 */
	public function __construct($om)
	{
		$this->om = $om;
	}

	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$transformer = new ProviderTransformer($this->om);
		$builder->addViewTransformer($transformer);
	}

	public function getParent()
	{
    	return 'listing';
	}

// 	public function setDefaultOptions(OptionsResolverInterface $resolver)
// 	{
//     	$resolver->setDefaults(array(
//         	'compound' => false,
//     	));
// 	}

	public function getName()
	{
		return 'provider_selector';
	}
}