<?php
namespace HealthCareAbroad\AdvertisementBundle\Form;

use HealthCareAbroad\AdvertisementBundle\Form\DataTransformer\AdvertisementTypeConfigurationTransformer;

use HealthCareAbroad\AdvertisementBundle\Form\ListType\AdvertisementPropertyNameListType;

use Symfony\Component\Validator\Constraints\NotBlank;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


class AdvertisementTypeFormType extends AbstractType
{
    protected $properties;
    
    public function __construct($properties)
    {
        $this->properties = $properties;
    }
    
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->add('name', 'text', array('label' => 'Name: '));

		$builder->add('title', 'checkbox', array('virtual' => true, 'attr' => array('checked' => true, 'disabled' => true)));
		$builder->add('description', 'checkbox', array('virtual' => true, 'attr' => array('checked' => true, 'disabled' => true)));
		
		$builder->add('sdf', new AdvertisementPropertyNameListType(), array('virtual' => true));

// 		$builder->add(
//             $builder->create('AdvertisementTypeConfigurations', new AdvertisementPropertyNameListType())
// 		            ->addModelTransformer()
//         );

		
// 		foreach($this->properties as $property){
// 		    $attr = array('label' => $property->getLabel(), 'virtual' => true, 'value' => $property->getId());
// 		    $builder->add($property->getName(), 'checkbox', $attr);
// 		}
	}

	// How does it work?
	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
	    $resolver->setDefaults(array(
			'data_class' => 'HealthCareAbroad\AdvertisementBundle\Entity\AdvertisementType',
		));
	}

	public function getName()
	{
		return 'advertisementType';
	}
}
