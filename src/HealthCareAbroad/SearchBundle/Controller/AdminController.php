<?php

namespace HealthCareAbroad\SearchBundle\Controller;

use HealthCareAbroad\SearchBundle\Services\Admin\SearchAdminPagerService;

use HealthCareAbroad\SearchBundle\Form\FilterFormType;
use HealthCareAbroad\InstitutionBundle\Entity\InstitutionStatus;
use HealthCareAbroad\SearchBundle\Services\Admin\SearchResultBuilder;
use HealthCareAbroad\MedicalProcedureBundle\Form\ListType\MedicalCenterListType;
use HealthCareAbroad\MedicalProcedureBundle\Entity\MedicalCenter;

use HealthCareAbroad\SearchBundle\Constants;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;

use HealthCareAbroad\SearchBundle\Form\AdminDefaultSearchType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AdminController extends Controller
{
	public function showWidgetAction($context)
	{
		if ('admin' == $context) {
			$form = $this->createForm(new AdminDefaultSearchType());    		
    		
    		return $this->render('SearchBundle:Admin:searchWidget.html.twig', array('form' => $form->createView()));
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
		
		if(!$searchCriteria){
		    
		    $searchCriteria['category'] = $request->get('category');
		    $searchCriteria['term'] = $request->get('term');
		}
		
		if($request->get('page')){
		    $searchCriteria['page'] = $request->get('page');
		}else{
		    $searchCriteria['page'] = 1;
		}
		
		$isDoctor = false;
		$route = "";
		switch ($searchCriteria['category']) {
			case Constants::SEARCH_CATEGORY_INSTITUTION:
				$varName = 'institutions';
				break;
			case Constants::SEARCH_CATEGORY_CENTER:
				$varName = 'medicalCenters';
				break;

			case Constants::SEARCH_CATEGORY_PROCEDURE_TYPE:
				$varName = 'procedureTypes';
				break;
		
			case Constants::SEARCH_CATEGORY_DOCTOR:
				$varName = 'doctors';
				$isDoctor = true;
				break;
				
			case Constants::SEARCH_CATEGORY_SPECIALIZATION:
			    $varName = 'specialization';
			    break;
			    
			case Constants::SEARCH_CATEGORY_SUB_SPECIALIZATION:
			    $varName = 'sub-specialization';
				break;
		}
		$p = new SearchAdminPagerService();
		$params = array(
						"data" => $this->get('services.admin_search')->search($searchCriteria, $p),
						"pager" => $p->getPager(),
		                "isDoctor" => $isDoctor,
		                "category" => $searchCriteria['category'],
						"term" => $searchCriteria['term'],
		);
		
		return $this->render('SearchBundle:Admin:searchResult.html.twig',$params);
	}
}
