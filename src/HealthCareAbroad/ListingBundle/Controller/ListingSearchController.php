<?php
namespace HealthCareAbroad\ListingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Reponse;

class ListingSearchController extends Controller
{
	public function showFormAction() {
		$searchForm = 'default';
		
		if ('default' != $searchForm) {
			echo 'Unimplemented';
			return;
		}
		
		return $this->render('ListingBundle:Search:searchBox.html.twig');
	}
	
	public function searchAction(Request $request)
	{
		$searchTerm = $request->get('searchTerm');
		$request->getSession()->setFlash('notice', 'You searched for: '.$searchTerm);		
		$listings = $this->get("services.listing_search")->getListings(array('searchTerm' => $searchTerm));
		
    	return $this->render('ListingBundle:Default:index.html.twig', array('listings'=>$listings));		
	}
}