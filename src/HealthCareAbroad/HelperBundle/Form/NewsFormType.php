<?php
namespace HealthCareAbroad\HelperBundle\Form;

use Symfony\Component\Validator\Constraints\NotBlank;

use HealthCareAbroad\HelperBundle\Entity\News;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


class CountryFormType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$status = array(
				News::STATUS_ACTIVE => 'publish',
				News::STATUS_INACTIVE => 'draft',
		);

		$builder->add('Title', 'text', array('constraints'=>array(new NotBlank())));
		$builder->add('Desctription', 'text', array('constraints'=>array(new NotBlank())));
		$builder->add('status', 'choice', array('choices'=>$status));
	}

	// How does it work?
	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
		$resolver->setDefaults(array(
				'data_class' => 'HealthCareAbroad\HelperBundle\Entity\News',
		));
	}

	public function getName()
	{
		return 'news';
	}
}
