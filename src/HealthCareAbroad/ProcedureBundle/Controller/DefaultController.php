<?php

namespace HealthCareAbroad\ProcedureBundle\Controller;


use HealthCareAbroad\ProcedureBundle\ProcedureBundle;

use HealthCareAbroad\HelperBundle\Entity\Tag;

use HealthCareAbroad\ProcedureBundle\Form\ProcedureType;

use HealthCareAbroad\ProcedureBundle\Form\DataTransformer\TagToObjectTransformer;

use HealthCareAbroad\ProcedureBundle\Entity\MedicalProcedure;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
		$criteria = array('status'=> 1);
		$procedures = $this->getDoctrine()->getEntityManager()->getRepository('ProcedureBundle:MedicalProcedure')->findBy($criteria);
    	$data = array('procedures'=>$procedures);
    	return $this->render('ProcedureBundle:Default:index.html.twig', $data);
    }


    public function addAction()
    {
    	$procedure = new MedicalProcedure();
    	$form = $this->createForm(new ProcedureType(), $procedure);
    	$params = array('form' => $form->createView());
    	return $this->render('ProcedureBundle:Default:create.html.twig', $params);
    }
    
    public function editAction($id)
    {
    	$listing = $this->get("services.listing")->getListing($id);
    	$listing = $this->get("services.listing")->populateLocations($listing);
    
    	$form = $this->createForm(new ListingType(), $listing);
    	$params = array('form' => $form->createView(), 'id' => $listing->getId());
    	return $this->render('ListingBundle:Default:create.html.twig', $params);
    }
    
    public function saveAction()
    {
    	$request = $this->getRequest();
    	
    	if ('POST' == $request->getMethod()) {
    		$data = $request->get('procedure');

			$procedure = $data['id'] 
				? $this->getDoctrine()->getEntityManager()->getRepository('ProcedureBundle:MedicalProcedure')->find($data['id']) 
				: new MedicalProcedure();
			
			$form = $this->createForm(new ProcedureType($this->getDoctrine()->getEntityManager()), $procedure);
    		$form->bind($request);

    		$procedure = $form->getData();
    		var_dump($procedure); exit;
     		
    		//TODO  Validation Not Working!
    		if (!$form->isValid()) {
    	   
    			// Saving Listing
    			$listing = $this->get("services.listing")->saveListing($form->getData());
    
    			// Saving Listing Locations
    			$listingLocations = $listingData['locations'];
    			foreach($listingLocations as $each) {
    				$location = $each['id'] ? $this->get("services.listing_location")->getLocation($each['id']) : new ListingLocation();
    				$location->setListing($listing);
    					
    				$locationForm = $this->createForm(new LocationType($this->get('container')), $location);
    				$locationForm->bind($each);
    
    				// TODO - Temporary Fixed, City should not be fetched again.
    				$city = $this->getDoctrine()->getRepository('HelperBundle:City')->find($each['city']);
    				$locationForm->getData()->setCity($city);
    
    				$this->get("services.listing_location")->saveLocation($locationForm->getData());
    			}
    
    			$request->getSession()->setFlash('notice', 'New Listing has been added!');
    			return $this->redirect($this->generateUrl('listing_homepage'));
    		}
    	}
    }
    
    public function viewAction($id)
    {
    	$listing = $this->get("services.listing")->getListing($id);
    	$locations = $this->get('services.listing_location')->getLocationByListing($listing);
    
    	foreach($locations as $each) {
    		$listing->getLocations()->add($each);
    	}
    
    	return $this->render('ListingBundle:Default:listing.html.twig', array('listing' => $listing));
    }
    
    public function deleteAction($id)
    {
    	$this->get("services.listing")->deleteListing($id);
    
    	$this->getRequest()->getSession()->setFlash('notice', 'Listing has been deleted!');
    	return $this->redirect($this->generateUrl('listing_homepage'));
    }
}
