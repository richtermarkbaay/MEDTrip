<?php
namespace HealthCareAbroad\HelperBundle\Form;

use HealthCareAbroad\HelperBundle\Form\ListType\CountryListType;

use HealthCareAbroad\HelperBundle\Entity\GlobalAwardTypes;

use Symfony\Component\Validator\Constraints\NotBlank;

use HealthCareAbroad\HelperBundle\Entity\GlobalAward;
use HealthCareAbroad\HelperBundle\Form\ListType\AwardingBodyListType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class GlobalAwardFormType extends AbstractType
{	
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$status = array(GlobalAward::STATUS_ACTIVE => 'active', GlobalAward::STATUS_INACTIVE => 'inactive');
		$builder->add('type', 'choice', array('choices' => GlobalAwardTypes::getTypes(), 'expanded' => false, 'constraints'=>array(new NotBlank())));
		$builder->add('name', 'text', array('constraints'=>array(new NotBlank())));
		$builder->add('awardingBody', new AwardingBodyListType());
		$builder->add('country', CountryListType::NAME, array('empty_value' => 'Choose Country'));
		$builder->add('details', 'textarea');
		$builder->add('status', 'choice', array('choices'=>$status));
	}

	// How does it work?
	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
	    $resolver->setDefaults(array(
			'data_class' => 'HealthCareAbroad\HelperBundle\Entity\GlobalAward',
		));
	}

	public function getName()
	{
		return 'global_award';
	}
}
