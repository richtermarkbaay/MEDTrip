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
		$params['context'] = $request->get('context');
		
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
				$template = 'AdminBundle:Treatment:index.html.twig';
				$varName = 'procedureTypes';
				break;
		
			case Constants::SEARCH_CATEGORY_PROCEDURE:
				$template = 'AdminBundle:MedicalProcedure:index.html.twig';
				$varName = 'procedures';
				break;
		}
		
		var_dump($this->get('services.admin_search')->buildQueryBuilder($searchCriteria));exit;
		
		return $this->render($template,
						array("{$varName}" => $this->get('services.admin_search')->buildQueryBuilder($searchCriteria)
						));
		
	}
}
