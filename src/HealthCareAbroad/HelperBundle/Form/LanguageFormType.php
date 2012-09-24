<?php
namespace HealthCareAbroad\HelperBundle\Form;

use Symfony\Component\Validator\Constraints\NotBlank;

use HealthCareAbroad\AdminBundle\Entity\Language;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class LanguageFormType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$status = array(Language::STATUS_ACTIVE => 'active', Language::STATUS_INACTIVE => 'inactive');
		
		$builder->add('name', 'text', array('constraints'=>array(new NotBlank())));
		$builder->add('isoCode', 'text', array('constraints'=>array(new NotBlank())));
		$builder->add('status', 'choice', array('choices'=>$status));
	}
	
	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
		$resolver->setDefaults(array(
				'data_class' => 'HealthCareAbroad\AdminBundle\Entity\Language',
		));
	}

	public function getName()
	{
		return 'language';
	}
}
