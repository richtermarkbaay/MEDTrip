<?php
namespace HealthCareAbroad\FrontendBundle\Services;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionTypes;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Generates Frontend Breadcrumbs based on route name
 * @author Adelbert D. Silla
 *
 * Refactored code (Ham)
 */
class FrontendBreadcrumbService
{
    //FIXME: BreadcrumbWidgetTwigExtension is accessing this but this should
    //be made private.
    public $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function generateBreadcrumbs()
    {
        $request = $this->container->get('request');

        $routeParams = $request->attributes->get('_route_params');
        if (isset($routeParams['breadcrumbLabel'])) {
            return array(array('label' => $routeParams['breadcrumbLabel']));
        }

        switch ($request->attributes->get('_route', '')) {
            case 'frontend_search_results_keywords' :
                return array(array('label' => $this->slugToName($routeParams['keywords'])));

            case 'frontend_search_results_related' :
                return array(array('label' => $this->slugToName($routeParams['tag'])));
        }

        return $this->doGenerateBreadcrumbs($this->normalizeData($request->attributes));
    }

    /**
     * Breadcrumbs will be shown according to insertion order in the $breadcrumbs array
     *
     * @param array $data
     * @return array
     */
    private function doGenerateBreadcrumbs(array $data)
    {
        $breadcrumbs = array();

        if ($data['country']) {
            $breadcrumbs[] = array(
                'label' => $data['country']['name'],
                'url' => $this->generateUrl('frontend_search_results_countries', array('country' => $data['country']['slug'])));
        }
        if ($data['city']) {
            $breadcrumbs[] = array(
                'label' => $data['city']['name'],
                'url' => $this->generateUrl('frontend_search_results_cities', array('country' => $data['country']['slug'], 'city' => $data['city']['slug'])));
        }
        if ($data['specialization']) {
            $breadcrumbs[] = array(
                'label' => $data['specialization']['name'],
                'url' => $this->generateUrl('frontend_search_results_specializations', array('specialization' => $data['specialization']['slug'])));
        }
        if ($data['subSpecialization']) {
            $breadcrumbs[] = array(
                'label' => $data['subSpecialization']['name'],
                'url' => $this->generateUrl('frontend_search_results_subSpecializations', array('specialization' => $data['specialization']['slug'], 'subSpecialization' => $data['specialization']['slug'])));
        }
        if ($data['treatment']) {
            $breadcrumbs[] = array(
                'label' => $data['treatment']['name'],
                'url' => $this->generateUrl('frontend_search_results_treatments', array('specialization' => $data['specialization']['slug'], 'treatment' => $data['treatment']['slug'])));
        }
        if ($data['institution']) {
            switch ($data['institution']['type']) {
                case InstitutionTypes::MULTIPLE_CENTER:
                    $routeName = 'frontend_multiple_center_institution_profile';
                    break;
                case InstitutionTypes::SINGLE_CENTER:
                    $routeName = 'frontend_single_center_institution_profile';
                    break;
            }
            $breadcrumbs[] = array(
                'label' => $data['institution']['name'],
                'url' => $this->generateUrl($routeName, array('institutionSlug' => $data['institution']['slug']))
            );
            if ($data['institutionMedicalCenter']) {
                $breadcrumbs[] = array('label' => $data['institutionMedicalCenter']['name']);
            }
        }

        return $breadcrumbs;
    }

    private function normalizeData(ParameterBag $requestAttribs)
    {
        $institution = array();
        $institutionMedicalCenter = array();
        if ($institutionMedicalCenter = $requestAttribs->get('institutionMedicalCenter', null)) {
            $institution = $institutionMedicalCenter['institution'];
            $country = $institution['country'];
            $city = isset($institution['city']) ? $institution['city'] : array();
        } elseif ($institution = $requestAttribs->get('institution', null)) {
            $country = $institution['country'];
            $city = isset($institution['city']) ? $institution['city'] : array();
        } else {
            $country = $requestAttribs->get('country', array());
            $city = $requestAttribs->get('city', array());
        }

        return array(
            'country' => $country,
            'city' => $city,
            'specialization' => $requestAttribs->get('specialization', array()),
            'subSpecialization' => $requestAttribs->get('subSpecialization', array()),
            'treatment' => $requestAttribs->get('treatment', array()),
            'institution' => $institution,
            'institutionMedicalCenter' => $institutionMedicalCenter
        );
    }

    /**
     * Should be used sparingly. There are irreversible changes when slugifying
     * so sometimes we won't get back the original name.
     *
     * @param string $slug
     * @return string
     */
    private function slugToName($slug)
    {
        return ucwords(str_replace('-', ' ', $slug));
    }

    private function generateUrl($routeName, array $params = array())
    {
        return $this->container->get('router')->generate($routeName, $params);
    }
}