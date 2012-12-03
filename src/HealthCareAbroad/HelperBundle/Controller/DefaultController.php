<?php

namespace HealthCareAbroad\HelperBundle\Controller;


use Symfony\Component\HttpFoundation\Request;

use HealthCareAbroad\TreatmentBundle\Entity\SubSpecialization;

use HealthCareAbroad\TreatmentBundle\Entity\Specialization;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function error403Action()
    {
        return $this->render('HelperBundle:Default:error403.html.twig');
    }
    
    public function indexAction($name)
    {
        return $this->render('HelperBundle:Default:index.html.twig', array('name' => $name));
    }
    
    public function loadCitiesAction(Request $request)
    {
        $countryId = $request->get('countryId', 0);
        $selectedCity = $request->get('selectedCityId', 0);
    	$data = $this->get('services.location')->getGlobalCitiesListByContry($countryId);

		$response = new Response(json_encode($data));
		$response->headers->set('Content-Type', 'application/json');

		return $response;
    }
    
    public function getSpecializationAccordionEntryAction($specializationId)
    {
        $criteria = array('status' => Specialization::STATUS_ACTIVE, 'id' => $specializationId);

        $params['specialization'] = $this->getDoctrine()->getRepository('TreatmentBundle:Specialization')->findOneBy($criteria);

        if(!$params['specialization']) {
            $result = array('error' => 'Invalid Specialization');
    		$response = new Response(json_encode($result));
    		$response->headers->set('Content-Type', 'application/json');

    		return $response;
        }

        $groupBySubSpecialization = true;
        $params['subSpecializations'] = $this->getDoctrine()->getRepository('TreatmentBundle:Treatment')->getBySpecializationId($specializationId, $groupBySubSpecialization);
        $params['showCloseBtn'] = $this->getRequest()->get('showCloseBtn', true);
        $params['selectedTreatments'] = $this->getRequest()->get('selectedTreatments', array());

        return $this->render('HelperBundle:Widgets:specializationAccordionEntry.html.twig', $params);
    }


    public function searchTagsAction($term)
    {
		$data = $this->getDoctrine()->getEntityManager()->getRepository('HelperBundle:Tag')->searchTags($term);

    	$response = new Response(json_encode($data));
    	$response->headers->set('Content-Type', 'application/json');

    	return $response;
    }
    
    // TODO: DEPRECATED ??
    public function autoCompleteSearchAction()
    {
    	$data = array();
		$request = $this->getRequest();
		$section = $request->get('section', null);
		$term = $request->get('term', null);

		switch($section) {
			case 'specialization' :
				$data = $this->getDoctrine()->getEntityManager()->getRepository('TreatmentBundle:Specialization')->autoCompleteSearch($term);
				break;
			case 'procedure-type' :
				$data = array(); // TODO - Get Array Result
				break;
			case 'procedure' :
				$data = array(); // TODO - Get Array Result
				break;
		}

		$response = new Response(json_encode($data));
		$response->headers->set('Content-Type', 'application/json');		
		return $response;
    }
}