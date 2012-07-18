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

		return $this->render('ListingBundle:Default:create.html.twig', array('form' => $form->createView()));
    }    
    
    public function editAction($id)
    {
		$listing = $this->get("services.listing")->getListing($id);
		
		$listing->setLocations(new ArrayCollection()); // TODO - This line should not be necessary
		
		$locations = $this->get('services.listing_location')->getLocationByListing($listing);
		foreach($locations as $each) {
			$listing->getLocations()->add($each);
		}

		$form = $this->createForm(new ListingType(), $listing);
		return $this->render('ListingBundle:Default:create.html.twig', array('form' => $form->createView()));
    }
    
    public function saveAction(Request $request)
    {
    	$listing = new Listing();
     	$form = $this->createForm(new ListingType(), $listing);

		if ('POST' == $request->getMethod()) {
			$form->bindRequest($request);

			//TODO  Validation Not Working!
	    	if (!$form->isValid()) {

	    		// Saving Listing
				$listing = $this->get("services.listing")->saveListing($form->getData());

				// Saving Listing Location
				$listingLocations = $listing->getLocations();
				foreach($listingLocations as $location) {
					$this->get("services.listing_location")->saveLocation($location);
				}

				// Success Message
				$request->getSession()->setFlash('notice', 'New Listing has been added!');

				// Redirect
				return $this->redirect($this->generateUrl('listing_homepage'));
	    	}
		}

    }

    public function deleteAction(Request $request)
    {
    	
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