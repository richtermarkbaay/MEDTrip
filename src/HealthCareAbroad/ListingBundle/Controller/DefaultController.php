<?php
namespace HealthCareAbroad\ListingBundle\Controller;

use Doctrine\ORM\EntityManager;
use HealthCareAbroad\ListingBundle\Service\ListingData;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use HealthCareAbroad\ListingBundle\Entity\Listing;
use HealthCareAbroad\ProviderBundle\Entity\Provider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Reponse;
use HealthCareAbroad\ListingBundle\Form\Type\ListingType;



class DefaultController extends Controller
{
    public function indexAction(Request $request)
    {
    	//$client = new Guzzle\Service\Client('http://test.com/');
    	//var_dump($client);
    	
		//$listings = $this->get("listing.service")->getFullListingsByProviderId($providerId, array('provider'));
		for($i=0; $i<5; $i++) {
			$listing = new Listing();
			$listing->setTitle("Listing Title " . $i+1);
			$listing->setDescription("Listing Desc. Lorem ipsum dulor sit amit. " . $i+1);
			$listings[] = $listing;
		}
    	
		//var_dump($listings[0]); exit;
		$listings = array('listing1','listing2');
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
		$em = $this->get('doctrine')->getEntityManager();
		$s = $em->find('ListingBundle:Listing', $id);
		var_dump($s->getTitle()); exit;
		//$listing = $this->get("listing.service")->getListing($id);
		return $this->_createForm($listing);
    }
    
    public function addAction(Request $request)
    {
    	$data = $request->request->get('form');

		if(!isset($data['provider']))
			$data['provider'] = $request->getSession()->get('provider.id');

		//$listing = $this->get("listing.service")->addListing($providerId, $data);
		
		$request->getSession()->setFlash('notice', 'New Listing has been added!');
		return $this->redirect($this->generateUrl('ListingBundle_homepage'));
    }
    
    public function deleteAction(Request $request)
    {
    	
    }
    
    private function _createForm(Listing $listing)
    {
    	$isProvider = false;
    	$providers = array('ADelbert', 'Chris', 'Harold', 'Mike', 'Hazel');
    	$countries = array('USA', 'Canada', 'Japan', 'China');
    	$states = array('Washington', 'New York', 'Chicago', 'California');
    	$countries = array('USA', 'Canada', 'Japan', 'China');
    	$formBuilder = $this->createFormBuilder($listing);
    	if(!$isProvider)
    		$formBuilder->add('provider', 'choice', array('choices' => $providers));
    	 
    	$formBuilder->add('title','text')
    	->add('description','textarea')
    	->add('address', 'textarea', array("property_path" => false))
    	->add('state', 'choice', array('choices'=> $states, 'property_path'=> false))
    	->add('region', 'text', array('property_path'=> false))
    	->add('country', 'choice', array('choices'=> $countries, 'property_path'=> false));
 
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

    	$listing = $listingService->addListing($data);
    	
    	return new Response($listing);
    }
}