<?php

namespace HealthCareAbroad\SearchBundle\Controller;

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
    		throw new Exception('Undefined context.');
    	}
    	/*
    	<form action="/admin/search" method="post">
    		<table border="0" cellpadding="0" cellspacing="0">
    			<tr>
    				<td>
    					<input type="text" id="adminDefaultSearch_term" name="adminDefaultSearch[term]" required="required" class="top-search-inp" onblur="if (this.value==&quot;&quot;) {
    	this.value=&quot;Search&quot;;
    	}" onfocus="if (this.value==&quot;Search&quot;) {
    		this.value=&quot;&quot;;
    	}" value="Search" />
    	</td>\t\n\t\t\t<td><select id="adminDefaultSearch_    	
    	*/
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
}
