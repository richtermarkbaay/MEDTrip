<?php
/**
 * Twig extension for contact number by country
 * @author Chaztine Blance
 */
namespace HealthCareAbroad\HelperBundle\Twig;

use HealthCareAbroad\HelperBundle\Entity\RouteType;
use HealthCareAbroad\HelperBundle\Services\LocationService;
use HealthCareAbroad\HelperBundle\Entity\Country;

class FlagTwigExtension extends \Twig_Extension
{
    
    /**
     * @var \Twig_Environment
     */
    private $twig;
    private $service;
    
    public function setTwig($v)
    {
        $this->twig = $v;
    }
    public function __construct(LocationService $service)
    {
        $this->service = $service;
    }
    
    public function getFunctions()
    {
        return array(
            'render_contactNumber_widget' => new \Twig_Function_Method($this, 'render_contactCountryList_widget'),
            'render_countryJsonList_widget' => new \Twig_Function_Method($this, 'render_countryJsonList_widget'),
        );
    }
    
    public function render_contactCountryList_widget($string = null, $twigTemplate = null)
    {
        $code = array();
        $datas = array();
        $countryGlobalData = $this->service->getGlobalCountryList();
   
        $twigTemplate = \is_null($twigTemplate) ? 'HelperBundle:Widgets:flag_widget.html.twig' : $twigTemplate;
        
        foreach ($countryGlobalData as $var => $a){
            $datas[] = $a;
            $code[] =  array(
                        'id' => $a['abbr'],
                        'value' => $a['code'],
            );
        }
        $params = array( 'countryJson' => \json_encode($code),
                        'countryList' => $datas,
                        'inputId' => $string);
        return $this->twig->render($twigTemplate, $params);
    }
    
    public function render_countryJsonList_widget($cityId = null, $country = null, $valueContainer = null, $twigTemplate = null)
    {
        $countryGlobalData = $this->service->getGlobalCountryList();
        $code = array();
        $twigTemplate = \is_null($twigTemplate) ? 'HelperBundle:Widgets:fancy_country_widget.html.twig' : $twigTemplate;
        foreach ($countryGlobalData as $var => $a){
            $abbr = strtolower($a['abbr']);
            $code[] =  array(
                            'id' => $a['id'],
                            'custom_label' => $a['name']." <span class='flag16 ".$abbr."'> </span>",
                            'label' => $a['name']
            );
        }
        
        $params = array( 'countryJsonList' => \json_encode($code, JSON_HEX_APOS),
                        'cityId' => $cityId,
                        'country' => $country,
                        'valueContainer' => $valueContainer);
        
        return $this->twig->render($twigTemplate, $params);
    }
     public function getContactCountryList()
     {
         return $returnValue;
     }

     public function getName()
     {
         return 'flagList';
     }
    
}