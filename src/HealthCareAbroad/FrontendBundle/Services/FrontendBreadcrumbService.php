<?php

/**
 * Generates Frontend Breadcrumbs based on route name
 * @author Adelbert D. Silla
 */

namespace HealthCareAbroad\FrontendBundle\Services;

use HealthCareAbroad\TreatmentBundle\Entity\SubSpecialization;

use HealthCareAbroad\TreatmentBundle\Entity\Specialization;

use Doctrine\ORM\Query\Expr\Join;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\ContainerInterface;

use HealthCareAbroad\HelperBundle\Entity\City;
use HealthCareAbroad\HelperBundle\Entity\Country;

use HealthCareAbroad\InstitutionBundle\Entity\Institution;
use HealthCareAbroad\InstitutionBundle\Entity\InstitutionStatus;
use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenterStatus;

class FrontendBreadcrumbService
{
    public $container;
    public $doctrine;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->doctrine = $container->get('doctrine');
    }

    public function generateBreadcrumbs()
    {
        $request = $this->container->get('request');
        $route = $request->attributes->get('_route');

        $routeParams = $request->attributes->get('_route_params');
        $breadcrumbs = array();
        //var_dump($route, $routeParams);

        switch($route) {
            case 'frontend_single_center_institution_profile' :
            case 'frontend_multiple_center_institution_profile' :
                $slug = $routeParams['institutionSlug'];
                $institution = $this->container->get('services.institution')->getFullInstitutionBySlug($slug);
                $country = $institution->getCountry();

                $breadcrumbs[] = $this->_getCountryBreadcrumb($country);
                if($city = $institution->getCity()) {
                    $breadcrumbs[] = $this->_getCityBreadcrumb($city);
                }
                $breadcrumbs[] = array('label' => $institution->getName());
                break;

            case 'frontend_institutionMedicaCenter_profile' :
                $slug = $routeParams['imcSlug'];

                $institutionMedicalCenter = $this->container->get('services.institution_medical_center')->getFullInstitutionMedicalCenterBySlug($slug);
                $institution = $institutionMedicalCenter->getInstitution();
    
                $breadcrumbs[] = $this->_getCountryBreadcrumb($institution->getCountry());
                if($city = $institution->getCity()) {
                    $breadcrumbs[] = $this->_getCityBreadcrumb($city);
                }
                $breadcrumbs[] = $this->_getInstitionBreadcrumb($institution);
                $breadcrumbs[] = array('label' => $institutionMedicalCenter->getName());
                break;

            case 'frontend_search_results_countries' :
                $breadcrumbs[] = array('label' => $this->_slugToName($routeParams['country']));
                break;

            case 'frontend_search_results_cities' :
                $breadcrumbs[] = $this->_getCountryBreadcrumb($routeParams['country']);
                $breadcrumbs[] = array('label' => $this->_slugToName($routeParams['city']));
                break;

            case 'frontend_search_results_specializations' :
                $breadcrumbs[] = array('label' => $this->_slugToName($routeParams['specialization']));
                break;

            case 'frontend_search_results_subSpecializations' :
                $breadcrumbs[] = $this->_getSpecializationBreadcrumb($routeParams['specialization']);
                $breadcrumbs[] = array('label' => $this->_slugToName($routeParams['subSpecialization']));
                break;

            case 'frontend_search_results_treatments' :
                $breadcrumbs[] = $this->_getSpecializationBreadcrumb($routeParams['specialization']);
                
                $treatment = $this->doctrine->getManager()->getRepository('TreatmentBundle:Treatment')->findOneBySlug($routeParams['treatment']);

                if($treatment) {
                    $subSpecializations = $treatment->getSubSpecializations();
                    if(count($subSpecializations) === 1) {
                        $breadcrumbs[] = $this->_getSubSpecializationBreadcrumb($subSpecializations[0], $routeParams['specialization']);
                    }
                }

                $breadcrumbs[] = array('label' => $treatment->getName());
                break;

            case 'frontend_search_combined' :
                $em = $this->doctrine->getManager();
                
                $country = $em->getRepository('HelperBundle:Country')->find($routeParams['countryId']);
                if($country) {
                    $breadcrumbs[] = $this->_getCountryBreadcrumb($country->getSlug());                        
                }

                if(isset($routeParams['cityId']) && $routeParams['cityId']) {
                    $city = $em->getRepository('HelperBundle:City')->find($routeParams['cityId']);
                    if($city) {
                        if(!isset($country)) {
                            $breadcrumbs[] = $this->_getCountryBreadcrumb($city->getCountry()->getSlug());
                        }
                        $breadcrumbs[] = $this->_getCityBreadcrumb($city);                        
                    }
                }

                $specialization = $em->getRepository('TreatmentBundle:Specialization')->find($routeParams['specializationId']);
                if($specialization) {
                    $citySlug = isset($city) ? $city->getSlug() : '';
                    $breadcrumbs[] = $this->_getSpecializationWithDestinationBreadcrumb($specialization, $country->getSlug(), $citySlug);
                }

                if(isset($routeParams['subSpecializationId']) && $routeParams['subSpecializationId']) {
                    $subSpecialization = $em->getRepository('TreatmentBundle:SubSpecialization')->find($routeParams['subSpecializationId']);
                    if($subSpecialization) {
                        $breadcrumbs[] = $this->_getSubSpecializationWithDestinationBreadcrumb($subSpecialization, $specialization->getSlug(), $country->getSlug(), $citySlug);                  
                    }
                }

                if(isset($routeParams['treatmentId']) && $routeParams['treatmentId']) {
                    $treatment = $em->getRepository('TreatmentBundle:Treatment')->find($routeParams['treatmentId']);
                    if($treatment) {
                        $subSpecializations = $treatment->getSubSpecializations();
                        if(count($subSpecializations) === 1) {
                            $citySlug = isset($city) ? $city->getSlug() : '';
                            $breadcrumbs[] = $this->_getSubSpecializationWithDestinationBreadcrumb($subSpecializations[0], $specialization->getSlug(), $country->getSlug(), $citySlug);
                        }
                        $breadcrumbs[] = array('label' => $treatment->getName());
                    }
                }
                break;

            /* TODO - Removed when ready */
            case 'frontend_search_results_keywords' :
                $breadcrumbs[] = array('label' => $this->_slugToName($routeParams['keywords']));
                break;

            /* TODO - Add Country and City */
            case 'frontend_search_results_related' :
                $breadcrumbs[] = array('label' => $this->_slugToName($routeParams['tag']));
                break;

            default :
                if(isset($routeParams['breadcrumbLabel'])) {
                    $breadcrumbs = array(array('label' => $routeParams['breadcrumbLabel']));
                }
                break;
        }

        return $breadcrumbs;
    }


    /** 
     * @param Institution $institution
     * @return multitype
     */
    private function _getInstitionBreadcrumb(Institution $institution)
    {
        $routeName = $this->container->get('services.institution')->getInstitutionRouteName($institution);

        return array(
            'label' => $institution->getName(),
            'url' => $this->_generateUrl($routeName, array('institutionSlug' => $institution->getSlug())),
        );
    }

    /** 
     * @param String slug or Country object $country
     * @return multitype
     */
    private function _getCountryBreadcrumb($country)
    {
        $name = is_object($country) ? $country->getName(): $this->_slugToName($country);

        return array(
            'label' => $name,
            'url' => $this->_generateUrl('frontend_search_results_countries', array('country' => $country))
        );       
    }

    /**
     * 
     * @param City $city
     * @return multitype
     */
    private function _getCityBreadcrumb(City $city)
    {
        $routeParams = array('country' => $city->getCountry()->getSlug(),'city' => $city->getSlug());

        return array(
            'label' => $city->getName(),
            'url' => $this->_generateUrl('frontend_search_results_cities', $routeParams)
        );
    }

    /**
     * @param String slug or Specialization object $specialization
     * @return multitype
     */
    private function _getSpecializationBreadcrumb($specialization)
    {
        $name = is_object($specialization) ? $specialization->getName(): $this->_slugToName($specialization);
    
        return array(
            'label' => $name,
            'url' => $this->_generateUrl('frontend_search_results_specializations', array('specialization' => $specialization))
        );
    }

    /**
     * Breadcrumb for Combined Search
     * @param String slug or Specialization object, countrySlug, citySlug
     * @return multitype
     */
    private function _getSpecializationWithDestinationBreadcrumb(Specialization $specialization, $country, $city)
    {
        $params = array( 'specialization' => $specialization->getSlug(), 'country' => $country);
        if($city) {
            $params['city'] = $city;
            $routeName = 'frontend_search_combined_countries_cities_specializations';
        } else {
            $routeName = 'frontend_search_combined_countries_specializations';
        }

        return array('label' => $specialization->getName(), 'url' => $this->_generateUrl($routeName, $params));
    }

    /**
     * @param String slug or SubSpecialization object, String slug or Specialization object
     * @return multitype
     */
    private function _getSubSpecializationBreadcrumb($subSpecialization, $specialization = '')
    {
        if(is_object($subSpecialization)) {
            $name = $subSpecialization->getName();
            $specialization = $subSpecialization->getSpecialization()->getSlug();
            $subSpecialization = $subSpecialization->getSlug();
            
        } else {
            $name = $this->_slugToName($subSpecialization);
        }
        
        $params = array('specialization' => $specialization, 'subSpecialization' => $subSpecialization);
        return array(
            'label' => $name,
            'url' => $this->_generateUrl('frontend_search_results_subSpecializations', $params)
        );
    }

    /**
     * Breadcrumb for Combined Search
     * @param SubSpecialization object, specializationSlug, countrySlug, citySlug
     * @return multitype
     */
    private function _getSubSpecializationWithDestinationBreadcrumb(SubSpecialization $subSpecialization, $specialization, $country, $city)
    {
        $params = array('specialization' => $specialization, 'subSpecializations' => $subSpecialization->getSlug(), 'country' => $country);
        if($city) {
            $params['city'] = $city;
            $routeName = 'frontend_search_combined_countries_cities_specializations__subSpecializations';
        } else {
            $routeName = 'frontend_search_combined_countries_specializations__subSpecializations';
        }
    
        return array('label' => $subSpecialization->getName(), 'url' => $this->_generateUrl($routeName, $params));
    }

    private function _slugToName($slug)
    {
        return ucwords(str_replace('-', ' ', $slug));        
    }

    private function _generateUrl($routeName, array $params = array())
    {
        return $this->container->get('router')->generate($routeName, $params);
    }
}