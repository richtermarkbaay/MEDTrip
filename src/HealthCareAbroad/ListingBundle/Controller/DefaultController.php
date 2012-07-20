<?php
namespace HealthCareAbroad\ListingBundle\Controller;


use Doctrine\Common\Collections\ArrayCollection;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use HealthCareAbroad\ListingBundle\Entity\Listing;
use HealthCareAbroad\ListingBundle\Entity\ListingLocation;
use HealthCareAbroad\ListingBundle\Form\ListingType;
use HealthCareAbroad\ListingBundle\Form\LocationType;
use HealthCareAbroad\ProviderBundle\Entity\Provider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Reponse;



class DefaultController extends Controller
{
    public function indexAction(Request $request)
    {
		$listings = $this->get("services.listing")->getListings(1);
    	$data = array('listings'=>$listings);
    	return $this->render('ListingBundle:Default:index.html.twig', $data);
    }
    
    public function addAction()
    {
    	$listing = new Listing();
		$listing->getLocations()->add(new ListingLocation());
		$form = $this->createForm(new ListingType(), $listing);
		$params = array('form' => $form->createView());
		return $this->render('ListingBundle:Default:create.html.twig', $params);
    }    
    
    public function editAction($id)
    {
		$listing = $this->get("services.listing")->getListing($id);
		$listing = $this->get("services.listing")->populateLocations($listing);

		$form = $this->createForm(new ListingType(), $listing);
		$params = array('form' => $form->createView(), 'id' => $listing->getId());
		return $this->render('ListingBundle:Default:create.html.twig', $params);
    }
    
    public function saveAction(Request $request)
    {
		if ('POST' == $request->getMethod()) {

			$listingData = $request->get('listing');
			$listing = $request->get('id') ? $this->get("services.listing")->getListing($request->get('id')) : new Listing(); 
			$form = $this->createForm(new ListingType(), $listing);
			$form->bind($listingData);

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
        
    public function testAction()
    {
    	$listingService = $this->get("services.listing");
    	$data = new ListingData();
    	$data->set('title', 'XXX title');
    	$data->set('description', 'Test description');
    	$data->set('status', 'false');
    	$data->set('providerId', 1);
		var_dump($data->get('title')); exit;
    	$listing = $listingService->addListing($data);
    	
    	return new Response($listing);
    }
}