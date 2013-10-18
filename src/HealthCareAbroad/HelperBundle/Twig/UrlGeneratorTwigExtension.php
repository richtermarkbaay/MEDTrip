<?php
namespace HealthCareAbroad\HelperBundle\Twig;

use HealthCareAbroad\HelperBundle\Entity\City;
use HealthCareAbroad\HelperBundle\Entity\Country;
use HealthCareAbroad\TreatmentBundle\Entity\Treatment;

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Extension for filters or functions that needs a url generator
 *
 * @author Harold Modesto <harold.modesto@chromedia.com>
 *
 */
class UrlGeneratorTwigExtension extends \Twig_Extension
{
    /**
     * 
     * @var Session
     */
    private $session;
    
    private $generator;
    
    private $chromediaApiUrl;
    
    private $chromediaAccountUrl;

    /** 
     * @param Session $session
     */
    public function setSession(Session $session)
    {
        $this->session = $session;
    }

    public function setChromediaApiUrl($url)
    {
        $this->chromediaApiUrl=$url;
    }

    public function setChromediaAccountUrl($url)
    {
        $this->chromediaAccountUrl = $url; 
    }
    
    public function __construct(UrlGeneratorInterface $generator)
    {
        $this->generator = $generator;
    }

    public function getFunctions()
    {
        return array(
            'get_treatment_url' => new \Twig_Function_Method($this, 'get_treatment_url'),
            'get_country_url' => new \Twig_Function_Method($this, 'get_country_url'),
            'get_city_url' => new \Twig_Function_Method($this, 'get_city_url'),
            'get_load_states_api_uri' => new \Twig_Function_Method($this, 'getLoadStatesApiUri'),
            'get_load_cities_api_uri' => new \Twig_Function_Method($this, 'getLoadCitiesApiUri'),
            'get_validate_email_uri' => new \Twig_Function_Method($this, 'getValidateEmailUri'),
            'get_update_cities_url' => new \Twig_Function_Method($this, 'getUpdateCitiesUrl'),
        );
    }

    public function get_treatment_url(Treatment $treatment)
    {
        return $this->generator->generate('frontend_search_results_treatments', array(
            'specialization' => $treatment->getSpecialization()->getSlug(),
            'treatment' => $treatment->getSlug()
        ), true);
    }
    
    public function get_country_url(Country $country)
    {
        $params = array('country' => $country->getSlug());

        return $this->generator->generate('frontend_search_results_countries', $params, true);
    }

    public function get_city_url(City $city)
    {
        $params = array('country' => $city->getCountry()->getSlug(), 'city' => $city->getSlug());

        return $this->generator->generate('frontend_search_results_cities', $params, true);
    }
    
    public function getValidateEmailUri()
    {
        return $this->chromediaAccountUrl.'/validate';
    }
    
    public function getLoadStatesApiUri()
    {
        $uri = $this->chromediaApiUrl.'/states';

        if($institutionId = $this->session->get('institutionId')) {
            $uri .= "?institution_id=$institutionId";
        }

        return $uri;
    }
    
    public function getLoadCitiesApiUri()
    {
        $uri = $this->chromediaApiUrl.'/cities';

        if($institutionId = $this->session->get('institutionId')) {
            $uri .= "?institution_id=$institutionId";
        }

        return $uri;
    }
    
    public function getUpdateCitiesUrl()
    {
        return $this->generator->generate('admin_city_update');
    }

    public function getName()
    {
        return 'url_generator';
    }
}