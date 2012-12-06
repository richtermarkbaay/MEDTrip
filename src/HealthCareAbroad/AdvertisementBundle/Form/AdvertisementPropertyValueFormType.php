<?php
namespace HealthCareAbroad\AdvertisementBundle\Form;

use HealthCareAbroad\AdvertisementBundle\Form\DataTransformer\AdvertisementCustomPropertyValueTransformer;

use HealthCareAbroad\AdvertisementBundle\Form\EventListener\AddAdvertisementCustomFieldSubscriber;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AdvertisementPropertyValueFormType extends AbstractType
{
    protected $em;
    
    public function __construct($em = null)
    {
        $this->em = $em;
    }
    
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
	    $builder->add('advertisementPropertyName', 'entity', array(
            'label' => ' ',
            'attr' => array('style' => 'display:none'),
            'class' => 'HealthCareAbroad\AdvertisementBundle\Entity\AdvertisementPropertyName','property' => 'name',
        ));
	}

	// How does it work?
	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
	    $resolver->setDefaults(array(
			'data_class' => 'HealthCareAbroad\AdvertisementBundle\Entity\AdvertisementPropertyValue',
		));
	}

	public function getName()
	{
		return 'advertisementPropertyValue';
	}
}
