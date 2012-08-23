<?php

namespace HealthCareAbroad\SearchBundle\Controller;

use HealthCareAbroad\SearchBundle\Form\FilterFormType;

use HealthCareAbroad\MedicalProcedureBundle\Form\ListType\MedicalCenterListType;

use HealthCareAbroad\MedicalProcedureBundle\Entity\MedicalCenter;

use HealthCareAbroad\SearchBundle\Constants;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Symfony\Component\HttpFoundation\Request;

use HealthCareAbroad\SearchBundle\Form\AdminDefaultSearchType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
	public function showWidgetAction($context)
	{
		if ('admin' == $context) {
			$form = $this->createForm(new AdminDefaultSearchType());    		
    		
    		return $this->render('SearchBundle:Default:searchWidget.html.twig', array('form' => $form->createView()));
    	} else {
    		throw new \Exception('Undefined context.');
    	}
    }
    
    /**
     * Main search function
     */
    public function initiateAction(Request $request)
    {
		$searchCriteria = $request->get('adminDefaultSearch', array());

		switch ($searchCriteria['category']) {
			case Constants::SEARCH_CATEGORY_INSTITUTION:
				$template = 'AdminBundle:Institution:index.html.twig';
				$varName = 'institutions';
				break;
		
			case Constants::SEARCH_CATEGORY_CENTER:
				$template = 'AdminBundle:MedicalCenter:index.html.twig';
				$varName = 'medicalCenters';
				break;

			case Constants::SEARCH_CATEGORY_PROCEDURE_TYPE:
				$template = 'AdminBundle:MedicalProcedureType:index.html.twig';
				$varName = 'procedureTypes';
				break;
		
			case Constants::SEARCH_CATEGORY_PROCEDURE:
				$template = 'AdminBundle:MedicalProcedure:index.html.twig';
				$varName = 'procedures';
				break;
		}
		
		return $this->render($template, array("{$varName}" => $this->get('services.search')->initiate($searchCriteria)));
	}
	
	public function showFiltersAction()
	{
		$filters = $routeParams = array();
		$request = $this->getRequest();

		$statusOptions = array(1 => 'Active', 0 => 'Inactive', 'all' => 'All');

		switch($request->get('route')) {
			case 'admin_medicalCenter_index' :
				break;
				
			case 'admin_procedureType_index' :
				$medicalCenters = $this->getDoctrine()->getEntityManager()->getRepository('MedicalProcedureBundle:MedicalCenter')->findByStatus(1);
				
				$options = array('all' => 'All');
				foreach($medicalCenters as  $each) {
					$options[$each->getId()] = $each->getName();
				}

				$selected = $request->get('selectedCenter');
				$filters['medicalCenter'] = array('label'=>'Medical Center', 'selected'=>$selected, 'options'=>$options);
				break;

			case 'admin_medicalProcedure_index' :
				$procedureTypes = $this->getDoctrine()->getEntityManager()->getRepository('MedicalProcedureBundle:MedicalProcedureType')->findByStatus(1);

				$options = array('all' => 'All');
				foreach($procedureTypes as  $each) {
					$options[$each->getId()] = $each->getName();
				}

				$filters['procedureType'] = array('label'=>'Procedure Type', 'selected'=>$request->get('selectedProcedureType'), 'options'=>$options);
				break;

			case 'admin_country_index' :
				break;
				
			case 'admin_city_index' :
				$countries = $this->getDoctrine()->getEntityManager()->getRepository('HelperBundle:Country')->findByStatus(1);
			
				$options = array('all' => 'All');
				foreach($countries as  $each) {
					$options[$each->getId()] = $each->getName();
				}

				$filters['country'] = array('label'=>'Country', 'selected'=>$request->get('selectedCountry'), 'options'=>$options);
				break;
				
			case 'admin_institution_manageProcedureTypes' :
				$id = $request->get('institution_id');
				$routeParams['id'] = $id;
				$institution = $this->getDoctrine()->getEntityManager()->getRepository('InstitutionBundle:Institution')->find($id);
				$institutionCenters = $institution->getInstitutionMedicalCenters();
				
				$options = array('all' => 'All');
 				foreach($institutionCenters as  $each) {
 					$center = $each->getMedicalCenter();
 					$options[$center->getId()] = $center->getName();
 				}
				
				$filters['medicalCenter'] = array('label'=>'Medical Center', 'selected'=>$request->get('selectedCenter'), 'options'=>$options);
				break;
				
			case 'admin_institution_index' :
				break;
			
			case 'admin_institution_manageCenters' :
				$routeParams['id'] = $request->get('institution_id');
				break;
		}


		$filters['status'] = array('label' => 'Status', 'selected' => $request->get('selectedStatus'), 'options' => $statusOptions);


		$url = $this->generateUrl($request->get('route'), $routeParams);
		$params = array('url' => $url, 'filters' => $filters);
		return $this->render('SearchBundle:Default:filters.html.twig', $params);
	}
}
