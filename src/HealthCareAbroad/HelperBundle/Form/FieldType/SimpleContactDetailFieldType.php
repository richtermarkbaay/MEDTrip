<?php

namespace HealthCareAbroad\HelperBundle\Form\FieldType;

use Doctrine\ORM\Query;

use HealthCareAbroad\HelperBundle\Form\EventListener\ContactDetailDataSubscriber;

use HealthCareAbroad\HelperBundle\Form\DataTransformer\CountryTransformer;

use HealthCareAbroad\HelperBundle\Services\LocationService;

use HealthCareAbroad\HelperBundle\Form\DataTransformer\ContactDetailDataTransformer;

use Symfony\Component\Validator\Constraints\NotBlank;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Symfony\Component\Form\AbstractType;

class SimpleContactDetailFieldType extends AbstractType
{
    /**
     * @var LocationService
     */
    private $locationService;
    
    public function setLocationService(LocationService $v)
    {
        $this->locationService = $v;    
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options=array())
    {
        $builder->addEventSubscriber(new ContactDetailDataSubscriber());
        $countryChoices = array();
        foreach ($this->locationService->getActiveCountries() as $country) {
            $code = (int)$country['countryCode'];
            $countryChoices[$country['id']] = $country['name']." (+{$code})";    
        }

        $countryList = $builder->create('country', 'choice', array('label' => "Country", 'choices' => $countryChoices))
            ->addModelTransformer(new CountryTransformer($this->locationService));
        
        $builder->add('type', 'hidden');
        $builder->add($countryList);
        $builder->add('area_code', 'text', array('required' => false, 'attr' => array('placeholder' => 'Area Code')));
        $builder->add('number', 'text', array('required' => false, 'attr' => array( 'placeholder' => 'Phone Number')));
        $builder->add('ext', 'text', array('required' => false));
        $builder->add('type', 'hidden', array('required' => false));
    }
    
    public function getName()
    {
        return 'simple_contact_detail';
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'HealthCareAbroad\HelperBundle\Entity\ContactDetail'
        ));
    }
}