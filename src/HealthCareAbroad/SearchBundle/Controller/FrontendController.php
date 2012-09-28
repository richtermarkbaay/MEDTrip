<?php
namespace HealthCareAbroad\SearchBundle\Controller;

use HealthCareAbroad\HelperBundle\Repository\CountryRepository;

use HealthCareAbroad\HelperBundle\Repository\CityRepository;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class FrontendController extends Controller
{
    public function showWidgetAction(Request $request)
    {
        $context = $request->get('context');

        switch ($context) {
            case 'homepage':
                //$form = $this->createForm();
                $template = 'SearchBundle:Frontend:searchWidget.html.twig';
                break;

            default:
                throw new \Exception('Undefined context');
        }

        return $this->render($template);
    }

    /**
     * There are five scenarios that will land us in this action:
     *
     *	1. search specific country ->
     *	/search/destinations/thailand
     *
     *	2. search specific city ->
     *	/search/destinations/thailand/bangkok
     *
     *	3. search specific procedure/procedure type ->
     *	/search/treatment/cosmetic-plastic-surgery/abdominoplasty
     *
     *	4. combination of city and procedure/procedure type ->
     *	/thailand/bangkok/cosmetic-plastic-surgery/abdominoplasty
     *
     *	5. combination of country and procedure type ->
     *	/thailand/cosmetic-plastic-surgery/abdominoplasty
     *
     * All searches are initiated from the homepage's main search widget.
     *
     * @param Request $request
     */
    public function searchHomepageAction(Request $request)
    {
        $searchTerms = $this->parseSearchTerms($request->get('treatment_id'), $request->get('destination_id'));

        $entityManager = $this->getDoctrine()->getManager();

        if ($searchTerms['countryId']) {
            $country = $entityManager->getRepository('HelperBundle:Country')->find($searchTerms['countryId']);
        }

        if ($searchTerms['cityId']) {
            $city = $entityManager->getRepository('HelperBundle:City')->find($searchTerms['cityId']);
        }

        if ($searchTerms['procedureTypeId']) {
            $procedureType = $entityManager->getRepository('MedicalProcedureBundle:MedicalProcedureType')->find($searchTerms['procedureTypeId']);
            $medicalCenter = $procedureType->getMedicalCenter();
        }

        switch ($searchTerms['context']) {
            case '_country':
                $parameters = array(
                    'country' => $country->getSlug()
               );
                $route = 'search_frontend_results_countries';

                break;

            case '_city':
                $parameters = array(
                    'country' => $country->getSlug(),
                    'city' => $city->getSlug()
                );
                $route = 'search_frontend_results_cities';

                break;

            case '_procedureType':
                $parameters = array(
                    'medicalCenter' => $medicalCenter->getSlug(),
                    'procedureType' => $procedureType->getSlug(),
                    'procedureId' => $searchTerms['procedureId']
                );
                $route = 'search_frontend_results_procedureTypes';

                break;

            case '_country_procedureType':
                $parameters = array(
                    'medicalCenter' => $medicalCenter->getSlug(),
                    'procedureType' => $procedureType->getSlug(),
                    'procedureId' => $searchTerms['procedureId'],
                    'country' => $country->getSlug()
                );

                //TODO: fix route
                $route = 'search_frontend_results_procedureTypes';

                break;

            case '_city_procedureType':
                $parameters = array(
                    'medicalCenter' => $medicalCenter->getSlug(),
                    'procedureType' => $procedureType->getSlug(),
                    'procedureId' => $searchTerms['procedureId'],
                    'country' => $country->getSlug(),
                    'city' => $city->getSlug()
                );

                //TODO: fix route
                $route = 'search_frontend_results_procedureTypes';

                break;

            default:
                throw new \Exception('Invalid search term/s');
        }

        return $this->redirect($this->generateUrl($route, $parameters));
    }

    /**
     * TODO: check for invalid inputs
     *
     * @param string $treatment
     * @param string $destination
     */
    private function parseSearchTerms($treatment, $destination)
    {
        $procedureTypeId = 0;
        $procedureId = 0;
        $countryId = 0;
        $cityId = 0;

        $searchTerms = array(
            'context' => '',
            'cityId' => 0,
            'countryId' => 0,
            'procedureTypeId' => 0,
            'procedureId' => 0
        );

        if (!empty($treatment)) {
            list($procedureTypeId, $procedureId) = \explode('-', $treatment);
            $searchTerms['procedureTypeId'] = $procedureTypeId;
            $searchTerms['procedureId'] = $procedureId;
        }

        if (!empty($destination)) {
            list($countryId, $cityId) = \explode('-', $destination);
            $searchTerms['countryId'] = $countryId;
            $searchTerms['cityId'] = $cityId;
        }

        $context = '';
        if ($cityId && $countryId) {
            $context = '_city';
        } else {
            $context = '_country';
        }
        if ($procedureTypeId) {
            $context = '_procedureType';
        }

        $searchTerms['context'] = $context;

        return $searchTerms;
    }

    public function searchResultsCountriesAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $country = $em->getRepository('HelperBundle:Country')->findOneBy(array('slug' => $request->get('country')));
        $centers = $em->getRepository('InstitutionBundle:InstitutionMedicalCenter')->getMedicalCentersByCountry($country);

        return $this->render('SearchBundle:Frontend:medicalCentersCountry.html.twig', array(
            'centers' => $centers,
            'country' => $country
        ));
    }

    public function searchResultsCitiesAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $city = $em->getRepository('HelperBundle:City')->findOneBy(array('slug' => $request->get('city')));
        $centers = $em->getRepository('InstitutionBundle:InstitutionMedicalCenter')->getMedicalCentersByCity($city);

        return $this->render('SearchBundle:Frontend:medicalCentersCity.html.twig', array(
            'centers' => $centers,
            'city' => $city
        ));
    }

    public function searchResultsTreatmentsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $procedure = null;
        if ((int)$request->get('procedureId')) {
            $procedure = $em->getRepository('MedicalProcedureBundle:MedicalProcedure')->find($request->get('procedureId'));
            $procedureType = $procedure->getMedicalProcedureType();
        } else {
            $procedureType = $em->getRepository('MedicalProcedureBundle:MedicalProcedureType')->findOneBy(array('slug' => $request->get('procedureType')));
        }

        $centers = $em->getRepository('InstitutionBundle:InstitutionMedicalCenter')->getMedicalCentersByTreatment($procedureType, $procedure);

        return $this->render('SearchBundle:Frontend:medicalCentersTreatment.html.twig', array(
            'centers' => $centers,
            'procedureType' => $procedureType,
            'procedure' => $procedure
        ));
    }

    public function searchResultsMedicalCentersAction(Request $request)
    {

    }

    public function ajaxLoadTreatmentsAction(Request $request)
    {
        $result = $this->get('services.search')->getTreatmentsByName($request->get('term', ''), $request->get('prevTerm'));

        return new Response(\json_encode($result), 200, array('Content-Type'=>'application/json'));
    }

    public function ajaxLoadDestinationsAction(Request $request)
    {
        $result = $this->get('services.search')->getDestinationsByName($request->get('term', ''), $request->get('prevTerm'));

        return new Response(\json_encode($result), 200, array('Content-Type'=>'application/json'));
    }

}