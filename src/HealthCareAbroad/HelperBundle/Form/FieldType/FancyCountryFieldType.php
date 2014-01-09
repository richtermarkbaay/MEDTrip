<?php
/**
 * Fancy Country Field Type
 *  
 * @author Adelbert D. Silla
 *
 */
namespace HealthCareAbroad\HelperBundle\Form\FieldType;

use Doctrine\ORM\Query;

use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;

use HealthCareAbroad\HelperBundle\Services\LocationService;
use HealthCareAbroad\HelperBundle\Form\DataTransformer\CountryTransformer;

class FancyCountryFieldType extends AbstractType
{
    const NAME = 'fancy_country';
    
    /**
     * @var LocationService
     */
    private $locationService;

    public function setLocationService(LocationService $service)
    {
        $this->locationService = $service;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(new CountryTransformer($this->locationService));
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        foreach($options as $key => $value) {
            $view->set($key, $value);
        }

        $view->set('countries', $this->_getCountryList());        
    }

    /** 
     * @return string
     */
    private function _getCountryList()
    {
        $countries = $this->locationService->getActiveCountries();
        $result = array();

        foreach ($countries as $each){
            $result[] =  array(
                'id' => $each['id'],
                'custom_label' => "<span class='flag16 ".strtolower($each['ccIso'])."'> </span> " . "<span class='item-label'>" .$each['name']. "</span>",
                'label' => $each['name']
            );
        }

        return \json_encode($result, JSON_HEX_APOS);;
    }

    public function getName()
    {
        return self::NAME;
    }

    public function getParent()
    {
        return 'hidden';
    }
}