<?php
namespace HealthCareAbroad\HelperBundle\Form\ListType;

use HealthCareAbroad\HelperBundle\Form\DataTransformer\CountryTransformer;

use HealthCareAbroad\HelperBundle\Form\DataTransformer\CountryArrayTransformer;

use Symfony\Component\Form\FormBuilderInterface;

use HealthCareAbroad\HelperBundle\Services\LocationService;

use Doctrine\ORM\EntityRepository;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class CountryListType extends AbstractType 
{	
    const NAME = 'country_list';

	public function buildForm(FormBuilderInterface $builder, array $options)
	{
	    
	}
	
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'property' => 'name',
            'class' => 'HealthCareAbroad\HelperBundle\Entity\Country',
            'query_builder' => function(EntityRepository $er){
                return $er->createQueryBuilder('u') ->orderBy('u.name', 'ASC');
            }            
        ));
    }

    public function getParent()
    {
        return 'entity';
    }

    public function getName()
    {
        return self::NAME;
    }
}