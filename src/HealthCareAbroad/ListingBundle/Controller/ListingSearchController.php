<?php
namespace HealthCareAbroad\ListingBundle\Controller;

use HealthCareAbroad\ListingBundle\Entity\ListingLocation;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Reponse;
use HealthCareAbroad\ListingBundle\Form\ListingSearchType;

class ListingSearchController extends Controller
{
	public function showFormAction() {
		$searchForm = 'default';
		
		if ('default' != $searchForm) {
			echo 'Unimplemented';
			return;
		}
		
		//return $this->render('ListingBundle:Search:searchBox.html.twig');
		$form = $this->createForm(new ListingSearchType());
		
		return $this->render('ListingBundle:Search:searchBox.html.twig', array('form' => $form->createView()));
	}
	
	public function searchAction(Request $request)
	{
		/*
		 * $defaultData = array('message' => 'Type your message here');
    $form = $this->createFormBuilder($defaultData)
        ->add('name', 'text')
        ->add('email', 'email')
        ->add('message', 'textarea')
        ->getForm();
		 */
		$data = $request->get('listingSearch');
		
		$request->getSession()->setFlash('notice', 'You searched for: '.$data['searchTerm']);		
		$listings = $this->get("services.listing_search")->getListings($data);
		
    	return $this->render('ListingBundle:Default:index.html.twig', array('listings'=>$listings));		
	}
}