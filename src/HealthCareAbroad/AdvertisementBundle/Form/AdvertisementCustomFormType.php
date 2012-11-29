<?php
namespace HealthCareAbroad\AdvertisementBundle\Form;

use HealthCareAbroad\AdvertisementBundle\Form\DataTransformer\AdvertisementCustomPropertyValueTransformer;

use HealthCareAbroad\AdvertisementBundle\Form\EventListener\AddAdvertisementCustomFieldSubscriber;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AdvertisementCustomFormType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
	    $builder->add('advertisementPropertyName', 'entity', array(
            'class' => 'HealthCareAbroad\AdvertisementBundle\Entity\AdvertisementPropertyName','property' => 'name',
        ));

// 	    $transformer = new AdvertisementCustomPropertyValueTransformer();
// 	    $builder->addModelTransformer($transformer);
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
