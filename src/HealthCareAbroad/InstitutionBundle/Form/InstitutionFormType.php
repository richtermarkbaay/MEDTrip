<?php

namespace HealthCareAbroad\InstitutionBundle\Form;


use Symfony\Component\Form\Util\PropertyPath;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormViewInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\MinLength;
use Symfony\Component\Validator\Constraints\MaxLength;
use Symfony\Component\DependencyInjection\ContainerInterface;
use HealthCareAbroad\InstitutionBundle\Form\InstitutionMedicalCenterBusinessHourFormType;
use Doctrine\Common\Persistence\ObjectManager;
use HealthCareAbroad\HelperBundle\Form\EventListener\LoadCitiesSubscriber;
use HealthCareAbroad\HelperBundle\Form\ListType\CountryListType;
use HealthCareAbroad\HelperBundle\Form\ListType\CityListType;

class InstitutionFormType extends AbstractType
{
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $subscriber = new LoadCitiesSubscriber($builder->getFormFactory());
        $builder->addEventSubscriber($subscriber);
        $cityId = 0;
        $builder->add('name','text', array('label' => 'Name'))
        ->add('description', 'textarea', array('label' => 'Details','attr' => array('class' => 'tinymce')))
        ->add('businessHours', 'hidden')
        ->add('email', 'text',array('property_path' => false));
        $builder->add('country', 'country_list', array('attr' => array('onchange'=>'Location.loadCities($(this), '. $cityId . ')'), 'property_path' => false));
    	$builder->add('city', 'city_list', array('property_path' => false));
    }
    
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
                        'data_class' => 'HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter'
        ));
    
    }
    public function getName()
    {
        return 'institutionForm';
    }
    
    
}