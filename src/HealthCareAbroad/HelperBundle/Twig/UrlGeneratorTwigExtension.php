<?php
namespace HealthCareAbroad\HelperBundle\Twig;
use HealthCareAbroad\HelperBundle\Entity\City;

use HealthCareAbroad\HelperBundle\Entity\Country;

use HealthCareAbroad\TreatmentBundle\Entity\Treatment;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Extension for filters or functions that needs a url generator
 *
 * @author Harold Modesto <harold.modesto@chromedia.com>
 *
 */
class UrlGeneratorTwigExtension extends \Twig_Extension
{
    private $generator;

    public function __construct(UrlGeneratorInterface $generator)
    {
        $this->generator = $generator;
    }

    public function getFunctions()
    {
        return array(
            'get_treatment_url' => new \Twig_Function_Method($this, 'get_treatment_url'),
            'get_country_url' => new \Twig_Function_Method($this, 'get_country_url'),
            'get_city_url' => new \Twig_Function_Method($this, 'get_city_url')
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

    public function getName()
    {
        return 'url_generator';
    }
}