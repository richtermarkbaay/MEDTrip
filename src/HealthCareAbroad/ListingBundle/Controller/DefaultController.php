<?php
namespace HealthCareAbroad\ListingBundle\Controller;

use HealthCareAbroad\ListingBundle\Service\ListingData;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use HealthCareAbroad\ListingBundle\Entity\Listing;
use HealthCareAbroad\ListingBundle\Form\ListingType;
use HealthCareAbroad\ProviderBundle\Entity\Provider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Reponse;



class DefaultController extends Controller
{
    public function indexAction(Request $request)
    {
		$listings = $this->get("services.listing")->getListings(2);
    	$data = array('listings'=>$listings);
    	return $this->render('ListingBundle:Default:index.html.twig', $data);
    }
    
    public function createAction()
    {
    	$em = $this->get('doctrine')->getEntityManager();
		$form = $this->createForm(new ListingType($em), new Listing());

		return $this->render('ListingBundle:Default:create.html.twig', array('form' => $form->createView()));
    }    
    
    public function editAction($id)
    {
		$em = $this->get('doctrine')->getEntityManager();
		//$listing = $em->find('ListingBundle:Listing', $id);
		$listing = $this->get("services.listing")->getListing($id);
		$l = new Listing();
		//$l->setProcedure()
		//$formBuilder = $this->createFormBuilder($listing);
		$form = $this->createForm(new ListingType($em), $l);
		$form->bindRequest($request);
		return $this->render('ListingBundle:Default:create.html.twig', array('form' => $form->createView()));

		return $form;
		//$listing = new Listing();
		//$listing->getTitle(); 
		//$listing = new Listing();
		//$listing->setTitle('test');
		return $this->_createForm($listing);
    }
    
    public function addAction()
    {
    	$em = $this->get('doctrine')->getEntityManager();
    	$data = $this->get('request')->get('listing');
 
    	$location = $data['location'];
    	unset($data['location']);

     	$form = $this->createForm(new ListingType(), new Listing());
    	$form->bind($data);
    	
    	var_dump($form->isValid()); exit;
    	if ($form->isValid())
    	{
    		$form->save();
    	}
    	echo 'bert'; exit;
    	var_dump($x); exit;
    			
    	$data['status'] = 0;
		//$listingData = new ListingData();
		//$listingData->set('status', 1);
		//foreach($data as $key => $val) {
		//$listingData->set($key, $val);
// 		}
    	//$form = $this->createForm(new ListingType($em), );
    	//$form->bindRequest($request)
    	
		$listing = $this->get("services.listing")->addListing($data);
		
		$request->getSession()->setFlash('notice', 'New Listing has been added!');
		return $this->redirect($this->generateUrl('ListingBundle_homepage'));
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