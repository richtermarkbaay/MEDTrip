<?php

namespace HealthCareAbroad\AdminBundle\Controller;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenterStatus;

use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\HttpFoundation\Request;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use HealthCareAbroad\AdminBundle\Form\GenericRankingItemFormType;
use HealthCareAbroad\SearchBundle\Services\SearchStates;
use HealthCareAbroad\SearchBundle\Services\SearchParameterService;
use HealthCareAbroad\TreatmentBundle\Entity\Specialization;
use HealthCareAbroad\PagerBundle\Adapter\ArrayAdapter;
use HealthCareAbroad\PagerBundle\Pager;
use HealthCareAbroad\TermBundle\Entity\SearchTerm;

class RankingController extends Controller
{
    public function institutionIndexAction(Request $request)
    {
        $institutions = $this->filteredResult;
        
        $rankingItemForm = $this->createForm(new GenericRankingItemFormType());
        
        return $this->render('AdminBundle:Ranking:institutionRankings.html.twig', array(
            //'institutionsJsonData' => \json_encode($data, JSON_HEX_APOS),
            'institutions' => $institutions,
            'rankingItemForm' => $rankingItemForm->createView()
        ));
    }
    
    public function institutionMedicalCenterIndexAction(Request $request)
    {
        $rankingItemForm = $this->createForm(new GenericRankingItemFormType());
        
        return $this->render('AdminBundle:Ranking:institutionMedicalCenterRankings.html.twig', array(
            'institutionMedicalCenters' => $this->filteredResult,
            'rankingItemForm' => $rankingItemForm->createView()
        ));
    }
    
    /**
     * Update ranking of an institution
     * 
     * @author acgvelarde
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function putUpdateInstitutionRankingAction(Request $request)
    {
        $form = $this->createForm(new GenericRankingItemFormType());
        $form->bind($request);
        $data = $form->getData();
        
        $institution = $this->getDoctrine()->getRepository('InstitutionBundle:Institution')->find($data['id']);
        if (!$institution){
            throw $this->createNotFoundException('Invalid institution');
        }
        
        $institution->setTotalClinicRankingPoints($data['rankingPoints']);
        $institutionService = $this->get('services.institution');
        $institutionService->save($institution);
        
        // check if this is a single center institution
        if ($institutionService->isSingleCenter($institution)){
            // also set the ranking point for the clinic
            $firstMedicalCenter = $institutionService->getFirstMedicalCenter($institution);
            if ($firstMedicalCenter){
                $firstMedicalCenter->setRankingPoints($data['rankingPoints']);
                $this->get('services.institution_medical_center')->save($firstMedicalCenter);
            }
        }
        
        //FIXME:  only flush the memcache related to this insitution
        $this->get('services.memcache')->flush();
        
        $responseData = array(
        	'id' => $institution->getId(),
            'rankingPoints' => $institution->getTotalClinicRankingPoints(),
            'error' => false
        );
        
        return new Response(\json_encode($responseData), 200, array('content-type' => 'application/json'));
    }
    
    /**
     * Update ranking of an institution medical center
     * 
     * @author acgvelarde
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function putUpdateInstitutionMedicalCenterRankingAction(Request $request)
    {
        $form = $this->createForm(new GenericRankingItemFormType());
        $form->bind($request);
        $data = $form->getData();
        
        $institutionMedicalCenter = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionMedicalCenter')->find($data['id']);
        if (!$institutionMedicalCenter){
            throw $this->createNotFoundException('Invalid medical center');	
        }
        $institution = $institutionMedicalCenter->getInstitution();
        $institutionMedicalCenter->setRankingPoints($data['rankingPoints']);
        $this->get('services.institution_medical_center')->save($institutionMedicalCenter);
        
        // check if this is a single center institution
        $institutionService = $this->get('services.institution');
        if ($institutionService->isSingleCenter($institution)){
            // also set the ranking point for the parent institution
            $institution->setTotalClinicRankingPoints($data['rankingPoints']);
            $institutionService->save($institution);
        }
        
        $responseData = array(
            'id' => $institutionMedicalCenter->getId(),
            'rankingPoints' => $institutionMedicalCenter->getRankingPoints(),
            'error' => false
        );
        
        //FIXME:  only flush the memcache related to this insitution and medical center
        $this->get('services.memcache')->flush();
        
        return new Response(\json_encode($responseData), 200, array('content-type' => 'application/json'));
    }
    
    /**
     * View ranking management page which mimics frontend search results
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     * @author acgvelarde
     */
    public function viewSearchResultsRankingAction()
    {
        $rankingItemForm = $this->createForm(new GenericRankingItemFormType());
        
        return $this->render('AdminBundle:Ranking:searchResultsRanking.html.twig', array(
        	'rankingItemForm' => $rankingItemForm->createView()
        ));
    }
    
    /**
     * Mimic frontend search process and get results for ranking management
     * 
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @author acgvelarde
     */
    public function processSearchAction(Request $request)
    {
        $searchParameterService = $this->get('services.search.parameters');
        $compiledSearch = $searchParameterService->compileRequest($request);
        $searchStateLabel = SearchStates::getSearchStateFromValue($compiledSearch->getSearchState());
        
        $searchVariables = $compiledSearch->getVariables();
        $searchService = $this->get('services.search');
        
        // flag on what ranking point the search service is using
        $isUsingInstitutionRankingPoints = false;
        switch($searchStateLabel) {
            // treatments only search
            case SearchStates::SPECIALIZATION_SEARCH:
                $specialization = $searchVariables[SearchParameterService::PARAMETER_KEY_SPECIALIZATION_ID];
                $results = $searchService->searchBySpecialization($specialization);
                $isUsingInstitutionRankingPoints = false; // uses clinic ranking points
                break;
            case SearchStates::SUB_SPECIALIZATION_SEARCH:
                $subSpecialization = $searchVariables[SearchParameterService::PARAMETER_KEY_SUB_SPECIALIZATION_ID];
                $results = $searchService->searchBySubSpecialization($subSpecialization);
                $isUsingInstitutionRankingPoints = false; // uses clinic ranking points
                break;
            case SearchStates::TREATMENT_SEARCH:
                $isUsingInstitutionRankingPoints = false; // uses clinic ranking points
                $treatment = $searchVariables[SearchParameterService::PARAMETER_KEY_TREATMENT_ID];
                $results = $searchService->searchByTreatment($treatment);
                break;
            // destinations only search
        	case SearchStates::COUNTRY_SEARCH:
        	    $isUsingInstitutionRankingPoints = true; // uses hospital ranking points
        	    $country = $searchVariables[SearchParameterService::PARAMETER_KEY_COUNTRY_ID];
        	    $results = $searchService->searchByCountry($country);
        	    break;
        	case SearchStates::CITY_SEARCH:
        	    $city = $searchVariables[SearchParameterService::PARAMETER_KEY_CITY_ID];
        	    $results = $searchService->searchByCity($city);
        	    $isUsingInstitutionRankingPoints = true; // uses hospital ranking points
        	    break;
        	// combination search
        	case SearchStates::COUNTRY_SPECIALIZATION_SEARCH:
        	    $isUsingInstitutionRankingPoints = false;
        	    $specialization = $searchVariables[SearchParameterService::PARAMETER_KEY_SPECIALIZATION_ID];
        	    $country = $searchVariables[SearchParameterService::PARAMETER_KEY_COUNTRY_ID];
        	    $results = $this->getDoctrine()->getManager()->getRepository('TermBundle:SearchTerm')
        	       ->findByFilters(array($specialization, $country));
        	    break;
        	case SearchStates::COUNTRY_SUB_SPECIALIZATION_SEARCH:
        	    $isUsingInstitutionRankingPoints = false;
        	    $subSpecialization = $searchVariables[SearchParameterService::PARAMETER_KEY_SUB_SPECIALIZATION_ID];
        	    $country = $searchVariables[SearchParameterService::PARAMETER_KEY_COUNTRY_ID];
        	    $results = $this->getDoctrine()->getManager()->getRepository('TermBundle:SearchTerm')
        	       ->findByFilters(array($subSpecialization, $country));
        	    break;
        	case SearchStates::COUNTRY_TREATMENT_SEARCH:
        	    $isUsingInstitutionRankingPoints = false;
        	    $treatment = $searchVariables[SearchParameterService::PARAMETER_KEY_TREATMENT_ID];
        	    $country = $searchVariables[SearchParameterService::PARAMETER_KEY_COUNTRY_ID];
        	    $results = $this->getDoctrine()->getManager()->getRepository('TermBundle:SearchTerm')
        	       ->findByFilters(array($treatment, $country));
        	    break;
        	case SearchStates::CITY_SPECIALIZATION_SEARCH:
        	    $isUsingInstitutionRankingPoints = false;
        	    $city = $searchVariables[SearchParameterService::PARAMETER_KEY_CITY_ID];
        	    $specialization = $searchVariables[SearchParameterService::PARAMETER_KEY_SPECIALIZATION_ID];
        	    $results = $this->getDoctrine()->getManager()->getRepository('TermBundle:SearchTerm')
        	       ->findByFilters(array($specialization, $city));
        	    break;
        	case SearchStates::CITY_SUB_SPECIALIZATION_SEARCH:
        	    $isUsingInstitutionRankingPoints = false;
        	    $subSpecialization = $searchVariables[SearchParameterService::PARAMETER_KEY_SUB_SPECIALIZATION_ID];
        	    $city = $searchVariables[SearchParameterService::PARAMETER_KEY_CITY_ID];
        	    $results = $this->getDoctrine()->getManager()->getRepository('TermBundle:SearchTerm')
        	       ->findByFilters(array($subSpecialization, $city));
        	    break;
        	case SearchStates::CITY_TREATMENT_SEARCH;
        	    $isUsingInstitutionRankingPoints = false;
        	    $city = $searchVariables[SearchParameterService::PARAMETER_KEY_CITY_ID];
        	    $treatment = $searchVariables[SearchParameterService::PARAMETER_KEY_TREATMENT_ID];
        	    $results = $this->getDoctrine()->getManager()->getRepository('TermBundle:SearchTerm')
        	       ->findByFilters(array($treatment, $city));
        	    break;
        }
        
        //TODO: for now, we only need the first page for ranking purposes
        $pagerAdapter = new ArrayAdapter($results);
        $pager = new Pager($pagerAdapter, array('page' => 1, 'limit' => 20));
        
        // we compose the response data
        $responseData = array(
        	'results' => array(),
            'totalNumberOfResults' => \count($results),
            'searchState' => $searchStateLabel
        );
        
        foreach ($pager->getResults() as $searchTerm){
            if ($searchTerm instanceof SearchTerm){
                $institution = $searchTerm->getInstitution();
                $institutionMedicalCenter = $searchTerm->getInstitutionMedicalCenter();
            	$arr = array(
    	           'institution' => array(
	                   'id' => $institution->getId(),
	                   'name' => $institution->getName(),
	                   'totalClinicRankingPoints' => $institution->getTotalClinicRankingPoints() ? $institution->getTotalClinicRankingPoints() : 0
            	   ),
        	       'institutionMedicalCenter' => array(
    	               'id' => $institutionMedicalCenter->getId(),
    	               'name' => $institutionMedicalCenter->getName(),
    	               'rankingPoints' => $institutionMedicalCenter->getRankingPoints() ? $institutionMedicalCenter->getRankingPoints() : 0 
    	           )
            	);
            	$arr['isUsingInstitutionRankingPoints'] = $isUsingInstitutionRankingPoints;
            	$arr['rankingPoints'] = $isUsingInstitutionRankingPoints
            	   ? $arr['institution']['totalClinicRankingPoints']
            	   : $arr['institutionMedicalCenter']['rankingPoints'];
            	
            	$responseData['results'][] = $arr;
            }
        	
        }
        
        return new Response(\json_encode($responseData), 200, array('content-type' => 'application/json'));
    }
}