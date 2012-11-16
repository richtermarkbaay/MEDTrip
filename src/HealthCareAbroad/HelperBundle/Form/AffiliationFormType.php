<?php
namespace HealthCareAbroad\HelperBundle\Form;

use Symfony\Component\Validator\Constraints\NotBlank;

use HealthCareAbroad\HelperBundle\Entity\Affiliation;
use HealthCareAbroad\HelperBundle\Form\ListType\AwardingBodiesListType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


class AffiliationFormType extends AbstractType
{	
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$status = array(Affiliation::STATUS_ACTIVE => 'active', Affiliation::STATUS_INACTIVE => 'inactive');

		$builder->add('name');
		$builder->add('awardingBodies', new AwardingBodiesListType());
		$builder->add('country', 'country_list');
		$builder->add('details');
		$builder->add('status', 'choice', array('choices'=>$status));
	}

	// How does it work?
	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
	    $resolver->setDefaults(array(
			'data_class' => 'HealthCareAbroad\HelperBundle\Entity\Affiliation',
		));
	}

	public function getName()
	{
		return 'affiliation';
	}
}
