<?php
namespace HealthCareAbroad\AdvertisementBundle\Form;

use HealthCareAbroad\AdvertisementBundle\Form\DataTransformer\AdvertisementCustomPropertyValueTransformer;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use HealthCareAbroad\AdvertisementBundle\Entity\AdvertisementPropertyValue;
use HealthCareAbroad\AdvertisementBundle\Form\EventListener\AdvertisementCustomPropertySubscriber;

class AdvertisementFormType extends AbstractType
{
    protected $em;
    
    function __construct($em = null)
    {
        $this->em = $em;
    }
    
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
	    $builder->add('institution', 'institution_list', array( 'virtual' => false, 'label' => 'Choose Institution'));
	    $builder->add('advertisementType', 'advertisementType_list', array('virtual' => false));
	    $builder->add('title');
	    $builder->add('description');
	    $builder->add('dateExpiry', 'date', array('label' => 'Expiry Date'));

	    $advertisement = $options['data'];
	    $customPropertySubscriber = new AdvertisementCustomPropertySubscriber($builder->getFormFactory(), $advertisement);

	    $builder->add(
            $builder->create('advertisementPropertyValues', 'collection', 
                array('type' => new AdvertisementPropertyValueFormType($this->em))
            )->addEventSubscriber($customPropertySubscriber)
        );
	    
	    $dataTransformer = new AdvertisementCustomPropertyValueTransformer($this->em, $advertisement);
	    $builder->addModelTransformer($dataTransformer);
	}

	// How does it work?
	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
	    $resolver->setDefaults(array(
			'data_class' => 'HealthCareAbroad\AdvertisementBundle\Entity\Advertisement',
		));
	}

	public function getName()
	{
		return 'advertisement';
	}
}
