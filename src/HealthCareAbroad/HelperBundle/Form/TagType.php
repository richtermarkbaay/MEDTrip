<?php
namespace HealthCareAbroad\HelperBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


class TagType extends AbstractType
{
	private $id;

	public function __construct($id = '')
	{
		$this->id = $id;
	}
	
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->add('id', 'hidden', array('virtual'=>true, 'attr' => array('value' => $this->id)));
		$builder->add('name', 'text');
		$builder->add('type', 'tagtype_list');
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
