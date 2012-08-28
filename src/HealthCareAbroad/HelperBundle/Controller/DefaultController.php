<?php

namespace HealthCareAbroad\HelperBundle\Controller;


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
    
    public function loadCitiesAction($countryId)
    {
    	$data = $this->get('services.location')->getListActiveCitiesByCountryId($countryId);

		$response = new Response(json_encode($data));
		$response->headers->set('Content-Type', 'application/json');

		return $response;
    }


    public function searchTagsAction($term)
    {
		$data = $this->getDoctrine()->getEntityManager()->getRepository('HelperBundle:Tag')->searchTags($term);

    	$response = new Response(json_encode($data));
    	$response->headers->set('Content-Type', 'application/json');

    	return $response;
    }
    
    public function autoCompleteSearchAction()
    {
    	$data = array();
		$request = $this->getRequest();
		$section = $request->get('section', null);
		$term = $request->get('term', null);

		switch($section) {
			case 'medical-center' :
				$data = $this->getDoctrine()->getEntityManager()->getRepository('MedicalProcedureBundle:MedicalCenter')->autoCompleteSearch($term);
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