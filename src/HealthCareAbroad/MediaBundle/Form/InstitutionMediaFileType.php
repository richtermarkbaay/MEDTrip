<?php
namespace HealthCareAbroad\MediaBundle\Form;

use HealthCareAbroad\MediaBundle\Form\Transformer\MediaFileTypeTransformer;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;


class InstitutionMediaFileType extends AbstractType
{
	private $media;
	
	/**
	 * 
	 * @param Media or null $media
	 */
	public function __construct($media = null) {
	    $this->media = $media;
	}

	public function buildForm(FormBuilderInterface $builder, array $options)
	{	
		$transformer = new MediaFileTypeTransformer();
		$builder->addModelTransformer($transformer);
	}
	
	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
	    $defaults = array('data_class' => '\HealthCareAbroad\MediaBundle\Entity\Media');

	    if($this->media) {
	        $defaults['virtual'] = true;
	    }

		$resolver->setDefaults($defaults);
	}	

	public function getParent()
	{
		return 'file';
	}

	public function getName()
	{
		return 'institution_media_file';
	}
}