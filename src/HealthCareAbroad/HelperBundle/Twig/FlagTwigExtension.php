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
    static $isFlagJsCodeLoaded = false;
    
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
            'render_contactNumber_widget' => new \Twig_Function_Method($this, 'render_contactCountryList_widget')
        );
    }
    

    public function render_contactCountryList_widget($twigTemplate = null)
    {

        $countries = $this->service->getGlobalCountryList();
        $twigTemplate = \is_null($twigTemplate) ? 'HelperBundle:Widgets:flag_widget.html.twig' : $twigTemplate;

        $params = array('countryList' => $countries, 'loadJsCode' => !self::$isFlagJsCodeLoaded, 'loadJs' => false);
        
        if(!static::$isFlagJsCodeLoaded) {
            static::$isFlagJsCodeLoaded = true;
            $params['loadJs'] = true;
        }
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