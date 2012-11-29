<?php
namespace HealthCareAbroad\AdvertisementBundle\Form;

use HealthCareAbroad\AdvertisementBundle\Form\EventListener\AddAdvertisementCustomFieldSubscriber;

use HealthCareAbroad\AdvertisementBundle\Entity\AdvertisementPropertyValue;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AdvertisementFormType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
	    $builder->add('institution', 'institution_list', array( 'virtual' => false, 'label' => 'Choose Institution'));
	    $builder->add('advertisementType', 'advertisementType_list', array('virtual' => false));
	    $builder->add('title');
	    $builder->add('description');


	    
	    $advertisement = $options['data'];
	    $addFieldSubscriber = new AddAdvertisementCustomFieldSubscriber($builder->getFormFactory(), $advertisement);

	    $builder->add(
            $builder->create('advertisementPropertyValues', 'collection', 
                array('type' => new AdvertisementCustomFormType())
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
