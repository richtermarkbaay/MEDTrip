<?php
namespace HealthCareAbroad\SearchBundle\Controller;

use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Session\Session;
use HealthCareAbroad\HelperBundle\Repository\CountryRepository;
use HealthCareAbroad\HelperBundle\Repository\CityRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


/**
 * TODO: Refactor whole class
 *
 */
class FrontendController extends Controller
{
    public function showWidgetAction(Request $request)
    {
        $options['context'] = $request->get('context');

        switch ($options['context']) {
            case 'homepage':
                //$form = $this->createForm();
                $options['destinationId'] = '0-0';
                $options['destinationLabel'] = 'destination';
                $options['treatmentId'] = '0-0';
                $options['treatmentLabel'] = 'treatment';

                break;

            case 'destinationsPage':
                $majorId = $request->get('countryId');
                $minorId = $request->get('cityId') ? $request->get('cityId') : 0;

                $options['destinationId'] = $majorId.'-'.$minorId;

                $options['destinationLabel'] = $request->get('cityId')
                    ? $request->get('cityName').', '.$request->get('countryName')
                    : $request->get('countryName');

                break;

            case 'treatmentsPage':
                $options['procedureTypeId'] = $request->get('procedureTypeId');
                $options['procedureTypeName'] = $request->get('procedureTypeName');

                if ($request->get('procedureId')) {
                    $options['procedureId'] = $request->get('procedureId');
                    $options['procedureName'] = $request->get('procedureName');
                }

                break;

            default:
                throw new \Exception('Undefined context');
        }

        return $this->render($template = 'SearchBundle:Frontend:searchWidget.html.twig', array('options' => $options));
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

        $session = $request->getSession();

        switch ($searchTerms['context']) {
            case '_country':
                $parameters = array(
                    'country' => $country->getSlug()
                );
                $route = 'search_frontend_results_countries';

                $session->getFlashBag()->set('search_terms', json_encode(array('countryId' => $country->getId())));

                break;

            case '_city':
                $parameters = array(
                    'country' => $country->getSlug(),
                    'city' => $city->getSlug()
                );
                $route = 'search_frontend_results_cities';

                $session->getFlashBag()->set('search_terms', json_encode(array('countryId' => $country->getId(), 'cityId' => $city->getId())));

                break;

            case '_procedureType':
                $parameters = array(
                    'medicalCenter' => $medicalCenter->getSlug(),
                    'procedureType' => $procedureType->getSlug(),
                    'procedureId' => $searchTerms['procedureId']
                );
                $route = 'search_frontend_results_procedureTypes';

                $value = array('medicalCenterId' => $medicalCenter->getId(), 'procedureTypeId' => $procedureType->getId());
                if (isset($searchTerms['procedureId'])) {
                    $value['procedureId'] = $searchTerms['procedureId'];
                }

                $session->getFlashBag()->set('search_terms', json_encode($value));

                break;

            case '_country_procedureType':

                // TODO: remove
                $parameters = array(
                    'medicalCenter' => $medicalCenter->getSlug(),
                    'procedureType' => $procedureType->getSlug(),
                    'procedureId' => $searchTerms['procedureId'],
                    'country' => $country->getSlug()
                );

                $variables = array(
                    'countryId'	=> $country->getId(),
                    'medicalCenterId' => $medicalCenter->getId(),
                    'procedureTypeId' => $procedureType->getId(),
                );

                $url = '/'.$country->getSlug().'/'.$medicalCenter->getSlug().'/'.$procedureType->getSlug();

                //TODO: this can produce a url with two differing content: one
                //with data specific to procedure and the other the more general
                //procedure type.
                if ($searchTerms['procedureId']) {
                    $variables['procedureId'] = $searchTerms['procedureId'];
                    $url .= '?procedureId='.$searchTerms['procedureId'];
                }

                $session->set(md5($request->getPathInfo()), json_encode($variables));

                //TODO: make a generator function or class

                return $this->redirect($url);

                break;//safeguard against removal of return statement

            case '_city_procedureType':

                // TODO: remove
                $parameters = array(
                    'medicalCenter' => $medicalCenter->getSlug(),
                    'procedureType' => $procedureType->getSlug(),
                    'procedureId' => $searchTerms['procedureId'],
                    'country' => $country->getSlug(),
                    'city' => $city->getSlug()
                );

                $variables = array(
                    'countryId'	=> $country->getId(),
                    'medicalCenterId' => $medicalCenter->getId(),
                    'procedureTypeId' => $procedureType->getId(),
                    'countryId' => $country->getId(),
                    'cityId' => $city->getId()
                );

                $url = '/'.$country->getSlug().'/'.$city->getSlug().'/'.$medicalCenter->getSlug().'/'.$procedureType->getSlug();

                if ($searchTerms['procedureId']) {
                    $variables['procedureId'] = $searchTerms['procedureId'];
                    $url .= '?procedureId='.$searchTerms['procedureId'];
                }

                $session->set(md5($request->getPathInfo()), json_encode($variables));

                return $this->redirect($url);

                break;//safeguard against removal of return statement

            default:
                throw new \Exception('Invalid search term/s');
        }

        return $this->redirect($this->generateUrl($route, $parameters));
    }

    public function searchResultsCountriesAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $countryId = null;
        if ($request->getSession()->has('search_terms')) {
            $searchTerms = json_decode($request->getSession()->get('search_terms'), true);

            $countryId = isset($searchTerms['countryId']) ? $searchTerms['countryId'] : 0;
        }

        if ($countryId) {
            $country = $em->getRepository('HelperBundle:Country')->find($countryId);
        } else {
            $country = $em->getRepository('HelperBundle:Country')->findOneBy(array('slug' => $request->get('country')));
        }

        $centers = $em->getRepository('InstitutionBundle:InstitutionMedicalCenter')->getMedicalCentersByCountry($country);

        $response = $this->render('SearchBundle:Frontend:medicalCentersCountry.html.twig', array(
            'centers' => $centers,
            'country' => $country
        ));

        $cookieName = $request->getPathInfo();
        if (!$request->cookies->has($cookieName)) {
            $cookie = new Cookie($cookieName, md5(json_encode(array('countryId'=> $countryId))));

            $response->headers->setCookie($cookie);
        }

        return $response;
    }

    public function searchResultsCitiesAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $cityId = null;
        if ($request->getSession()->has('search_terms')) {
            $searchTerms = json_decode($request->getSession()->get('search_terms'), true);

            $cityId = isset($searchTerms['cityId']) ? $searchTerms['cityId'] : 0;
        }

        if ($cityId) {
            $city = $em->getRepository('HelperBundle:City')->find($cityId);
        } else {
            $city = $em->getRepository('HelperBundle:City')->findOneBy(array('slug' => $request->get('city')));
        }

        $centers = $em->getRepository('InstitutionBundle:InstitutionMedicalCenter')->getMedicalCentersByCity($city);

        $response = $this->render('SearchBundle:Frontend:medicalCentersCity.html.twig', array(
            'centers' => $centers,
            'city' => $city
        ));

        $cookieName = $request->getPathInfo();
        if (!$request->cookies->has($cookieName)) {
            $cookie = new Cookie($cookieName, md5(json_encode(array('countryId'=> $city->getCountry()->getId(), 'cityId' => $cityId))));

            $response->headers->setCookie($cookie);
        }

        return $response;
    }

    public function searchResultsTreatmentsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $procedureId = null;
        $procedureTypeId = null;

        // Lookup session for ids
        if ($request->getSession()->has('search_terms')) {
            $searchTerms = json_decode($request->getSession()->get('search_terms'), true);

            $procedureTypeId = isset($searchTerms['procedureTypeId']) ? $searchTerms['procedureTypeId'] : 0;
            $procedureId = isset($searchTerms['procedureId']) ? $searchTerms['procedureId'] : 0;
        }

        // Else try the request object
        if (empty($procedureId)) {
            $procedureId = $request->get('procedureId', 0);
        }

        $procedure = null;
        $procedureType = null;

        if ($procedureId) {
            $procedure = $em->getRepository('MedicalProcedureBundle:MedicalProcedure')->find($procedureId);
            $procedureType = $procedure->getMedicalProcedureType();
        } else if ($procedureTypeId) {
            $procedureType = $em->getRepository('MedicalProcedureBundle:MedicalProcedureType')->find($procedureTypeId);
        } else {
            $procedureType = $em->getRepository('MedicalProcedureBundle:MedicalProcedureType')->findOneBy(array('slug' => $request->get('procedureType')));
        }

        $centers = $em->getRepository('InstitutionBundle:InstitutionMedicalCenter')->getMedicalCentersByTreatment($procedureType, $procedure);
        //$countries = $em->getRepository('InstitutionBundle:InstitutionMedicalProcedureType')->getCountriesWithProcedureType($procedureType);

        $countries = $this->appendCountryUrls(
            $this->get('services.search')->getCountriesWithProcedureType($procedureType), $procedureType
        );

        return $this->render('SearchBundle:Frontend:medicalCentersTreatment.html.twig', array(
            'centers' => $centers,
            'countries' => $countries,
            'procedureType' => $procedureType,
            'procedure' => $procedure
        ));
    }

    public function searchResultsMedicalCentersAction(Request $request)
    {

    }

    public function ajaxLoadTreatmentsAction(Request $request)
    {
        $tokens = $this->tokenizeSearchTerm($request->get('term', ''));

        $result = array();

        // TODO: We don't want to mess with the query to the db, as the behavior
        // or implementation is not yet final, so merge the results for now and
        // prune out the duplicates.
        foreach($tokens as $treatmentTerm) {
            $result += $this->get('services.search')->getTreatmentsByName($treatmentTerm, $request->get('prevTerm'));
        }
        $result = array_values(array_map("unserialize", array_unique(array_map("serialize", $result))));

        return new Response(json_encode($result), 200, array('Content-Type'=>'application/json'));
    }

    public function ajaxLoadDestinationsAction(Request $request)
    {
        $tokens = $this->tokenizeSearchTerm($request->get('term', ''));

        $result = array();
        // TODO: We don't want to mess with the query to the db, as the behavior
        // or implementation is not yet final, so merge the results for now and
        // prune out the duplicates.
        foreach($tokens as $destinationTerm) {
            $result = array_merge($result, $this->get('services.search')->getDestinationsByName($destinationTerm, $request->get('prevTerm')));
        }
        $result = array_values(array_map("unserialize", array_unique(array_map("serialize", $result))));

        return new Response(json_encode($result), 200, array('Content-Type'=>'application/json'));
    }

    private function tokenizeSearchTerm($term)
    {
        $tokens = array();

        if (!empty($term)) {
            $tokens = array_filter(array_map('trim', explode(',', $term)));
        }

        return $tokens;
    }

    private function appendCountryUrls($countries, $procedureType)
    {
        $center = $procedureType->getMedicalCenter();

        $modifiedCountries = array();
        foreach ($countries as $country) {
            $modifiedCountry['id'] = $country['id'];
            $modifiedCountry['name'] = $country['name'];
            $modifiedCountry['url'] = '/'.$country['name'].'/'.$center->getSlug().'/'.$procedureType->getSlug();

            $modifiedCountries[] = $modifiedCountry;
        }

        return $modifiedCountries;
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
            list($procedureTypeId, $procedureId) = explode('-', $treatment);
            $searchTerms['procedureTypeId'] = $procedureTypeId;
            $searchTerms['procedureId'] = $procedureId;
        }

        if (!empty($destination)) {
            list($countryId, $cityId) = explode('-', $destination);

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

}