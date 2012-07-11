<?php
namespace HealthCareAbroad\ListingBundle\Controller;

use Doctrine\ORM\EntityManager;
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
    	//$client = new Guzzle\Service\Client('http://test.com/');
    	//var_dump($client);

		$listings = $this->get("listing.service")->getListings(1);
    

    	$data = array('listings'=>$listings);
    	return $this->render('ListingBundle:Default:index.html.twig', $data);
    }
    
    public function createAction()
    {
		$listing = new Listing();
		return $this->_createForm($listing);
    }    
    
    public function editAction($id)
    {
		//$em = $this->get('doctrine')->getEntityManager();
		//$listing = $em->find('ListingBundle:Listing', $id);

		$listing = $this->get("listing.service")->getListing($id);

		//$formBuilder = $this->createFormBuilder($listing);
		$form = $this->createForm(new ListingType(), $listing);
		return $this->render('ListingBundle:Default:create.html.twig', array('form' => $form->createView()));

		return $form;
		//$listing = new Listing();
		//$listing->getTitle(); 
		//$listing = new Listing();
		//$listing->setTitle('test');
		return $this->_createForm($listing);
    }
    
    public function addAction(Request $request)
    {
    	$data = $request->request->get('form');

		if(!isset($data['provider']))
			$data['provider_id'] = $request->getSession()->get('provider.id');
		else 
			$data['provider_id'] = $data['provider'];
		
		unset($data['_token']);
		unset($data['provider']);
		
		$listingData = new ListingData();
		$listingData->set('status', 1);
		foreach($data as $key => $val) {
			$listingData->set($key, $val);
		}

		
		$listing = $this->get("listing.service")->addListing($listingData);
		
		$request->getSession()->setFlash('notice', 'New Listing has been added!');
		return $this->redirect($this->generateUrl('ListingBundle_homepage'));
    }
    
    public function deleteAction(Request $request)
    {
    	
    }
    
    private function _createForm(Listing $listing)
    {
    	$isProvider = false;
    	$providers = array(1 => 'ADelbert', 2=> 'Chris');
    	$countries = array('USA', 'Canada', 'Japan', 'China');
    	$states = array('Washington', 'New York', 'Chicago', 'California');
    	$countries = array('USA', 'Canada', 'Japan', 'China');
    	$formBuilder = $this->createFormBuilder($listing);
    	//if(!$isProvider)
    		//$formBuilder->add('provider', 'choice', array('choices' => $providers));
    	 
    	$formBuilder->add('title','text')
    	->add('description','textarea')
    	->add('address', 'textarea', array("property_path" => false));
    	//->add('state', 'choice', array('choices'=> $states, 'property_path'=> false))
    	//->add('region', 'text', array('property_path'=> false))
    	//->add('country', 'choice', array('choices'=> $countries, 'property_path'=> false));
 
    	$form = $formBuilder->getForm();
    	 
    	return $this->render('ListingBundle:Default:create.html.twig', array('form' => $form->createView()));
    }
    
    public function testAction()
    {
    	$listingService = $this->get("listing.service");
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