<?php

namespace HealthCareAbroad\HelperBundle\Form\ListType;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use HealthCareAbroad\HelperBundle\Services\LocationService;

use Symfony\Component\Form\AbstractType;

class CountryCodeListType extends AbstractType
{
    /**
     * @var LocationService
     */
    private $service;
    
    public function setLocationService(LocationService $v)
    {
        $this->service = $v;
    }
    
    public function getName()
    {
        return 'country_code_list';
    }
    
    public function getParent()
    {
        return 'choice';
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $countries = $this->service->getGlobalCountryList();
        $choices = array();
        foreach ($countries as $country) {
            $code = (int)$country['code'];
            if ($code) {
                $choices[$code] = $country['name'].' (+'.$code.')';
            }
        }
        $resolver->setDefaults(array(
            'choices' => $choices
        ));
    }
}