<?php
namespace HealthCareAbroad\MediaBundle\Form;

use HealthCareAbroad\MediaBundle\Entity\Media;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;


class MediaUploadImageType extends AbstractType
{
	private $media;
	
	public function __construct(Media $media = null)
	{
		$this->media = $media;
	}
	
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->add('photo', 'file');	
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
		return 'mediaUploadImage';
	}
}