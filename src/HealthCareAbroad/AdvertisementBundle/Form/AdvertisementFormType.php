<?php
namespace HealthCareAbroad\AdvertisementBundle\Form;

use HealthCareAbroad\AdvertisementBundle\Form\DataTransformer\AdvertisementCustomPropertyValueTransformer;

use HealthCareAbroad\AdvertisementBundle\Form\EventListener\AddAdvertisementCustomFieldSubscriber;

use HealthCareAbroad\AdvertisementBundle\Entity\AdvertisementPropertyValue;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

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

	    $advertisement = $options['data'];
	    $addFieldSubscriber = new AddAdvertisementCustomFieldSubscriber($builder->getFormFactory(), $advertisement, $this->em);

	    //$modelTransformer = new AdvertisementCustomPropertyValueTransformer($this->em);

	    $builder->add(
            $builder->create('advertisementPropertyValues', 'collection', 
                array('type' => new AdvertisementCustomFormType($em))
            )->addEventSubscriber($addFieldSubscriber)
        );
	    
	    //exit;
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
