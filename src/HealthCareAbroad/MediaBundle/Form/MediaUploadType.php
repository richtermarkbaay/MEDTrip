<?php
namespace HealthCareAbroad\MediaBundle\Form;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;


class MediaUploadType extends AbstractType
{
	private $backedEntity;
	
	public function __construct($entity)
	{
		$this->backedEntity = $entity;
	}
	
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		//$builder->add('photo', 'file');	
	}
	
	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
		if (isset($this->options['label'])) {
			$resolver->setDefaults(array(
					'label' => $this->options['label']
			));
		}	
	}	
	
	public function getParent()
	{
		return 'file';
	}
	
	public function getName()
	{
		return 'mediaUpload';
	}
}