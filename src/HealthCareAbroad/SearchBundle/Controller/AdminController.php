<?php

namespace HealthCareAbroad\SearchBundle\Controller;

use HealthCareAbroad\HelperBundle\Services\Filters\SearchResultListFilter;

use HealthCareAbroad\HelperBundle\Services\Filters\ListFilter;

use HealthCareAbroad\SearchBundle\Form\FilterFormType;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionStatus;

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
		
		$params = array(
						"datas" => $this->get('services.admin_search')->search($searchCriteria),
						"pager" => $this->get('services.admin_search')->pager,
		                "isDoctor" => $isDoctor,
		                "category" => $varName
		);
		
		return $this->render('SearchBundle:Admin:searchResult.html.twig',$params);
	}
}
