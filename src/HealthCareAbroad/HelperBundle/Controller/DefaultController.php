<?php

namespace HealthCareAbroad\HelperBundle\Controller;


use HealthCareAbroad\InstitutionBundle\Entity\InstitutionSpecialization;

use HealthCareAbroad\InstitutionBundle\Form\InstitutionSpecializationFormType;

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
    
    /**
     * Get accordion form for specialization
     * 
     * @param unknown_type $specializationId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getSpecializationAccordionEntryAction(Request $request)
    {
        $specializationId = $request->get('specializationId', 0);
        
        $criteria = array('status' => Specialization::STATUS_ACTIVE, 'id' => $specializationId);

        $params['specialization'] = $this->getDoctrine()->getRepository('TreatmentBundle:Specialization')->findOneBy($criteria);

        if(!$params['specialization']) {
            $result = array('error' => 'Invalid Specialization');

    		return new Response('Invalid Specialization', 404);
        }

        $groupBySubSpecialization = true;
        $form = $this->createForm(new InstitutionSpecializationFormType(), new InstitutionSpecialization(), array('em' => $this->getDoctrine()->getEntityManager()));
        $params['formName'] = InstitutionSpecializationFormType::NAME;
        $params['form'] = $form->createView();
        $params['subSpecializations'] = $this->getDoctrine()->getRepository('TreatmentBundle:Treatment')->getBySpecializationId($specializationId, $groupBySubSpecialization);
        $params['showCloseBtn'] = $this->getRequest()->get('showCloseBtn', true);
        $params['selectedTreatments'] = $this->getRequest()->get('selectedTreatments', array());

        $html = $this->renderView('HelperBundle:Widgets:specializationAccordionEntry.html.twig', $params);
//         $html = $this->renderView('HelperBundle:Widgets:testForm.html.twig', $params);
        //echo $html; exit;
        
        return new Response(\json_encode(array('html' => $html)), 200, array('content-type' => 'application/json'));
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