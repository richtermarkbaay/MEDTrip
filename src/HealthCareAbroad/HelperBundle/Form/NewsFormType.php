<?php
namespace HealthCareAbroad\HelperBundle\Form;

use Symfony\Component\Validator\Constraints\NotBlank;

use HealthCareAbroad\HelperBundle\Entity\News;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class NewsFormType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$status = array(News::STATUS_ACTIVE => 'active', News::STATUS_INACTIVE => 'inactive');
		
		$builder->add('title', 'text', array('constraints'=>array(new NotBlank())));
		$builder->add('description', 'textarea', array('constraints' => new NotBlank(), 'attr' => array('class' => 'tinymce')));
		
		$builder->add('status', 'choice', array('choices'=>$status));
	}
	
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
