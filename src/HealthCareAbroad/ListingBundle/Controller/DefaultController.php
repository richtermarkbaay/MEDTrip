<?php

namespace HealthCareAbroad\ListingBundle\Controller;

use HealthCareAbroad\ListingBundle\Service\ListingData;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('ListingBundle:Default:index.html.twig', array('name' => $name));
    }
    
    public function testAction()
    {
    	$listingService = $this->get("listing.service");
    	$data = new ListingData();
    	$data->set('title', 'XXX title');
    	$data->set('description', 'Test description');
    	$data->set('status', 'false');
    	$data->set('providerId', 1);

    	$listing = $listingService->addListing($data);
    	
    	return new Response($listing);
    }
}
