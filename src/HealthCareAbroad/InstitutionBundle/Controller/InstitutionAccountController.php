<?php 
/*
 * @author Chaztine Blance
 */
namespace HealthCareAbroad\InstitutionBundle\Controller;

use HealthCareAbroad\InstitutionBundle\Event\InstitutionBundleEvents;
use HealthCareAbroad\InstitutionBundle\Event\EditInstitutionEvent;
use HealthCareAbroad\InstitutionBundle\Form\InstitutionDetailType;
use HealthCareAbroad\InstitutionBundle\Entity\Institution;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use ChromediaUtilities\Helpers\SecurityHelper;
use JMS\SecurityExtraBundle\Annotation\PreAuthorize;

class InstitutionAccountController extends Controller
{
	 public function accountAction(){

	 	$institution = $this->getDoctrine()->getRepository('InstitutionBundle:Institution')->find($this->getRequest()->get('institutionId', null));

	 	$form = $this->createForm(new InstitutionDetailType(), $institution, array('profile_type' => false, 'hidden_field' => false));		

	 	return $this->render('InstitutionBundle:Institution:accountProfileForm.html.twig', array(
	 					'form' => $form->createView(),
	 					'institution' => $institution
	 	));
	 }
	 
	 public function saveAction(){
	 	
	 	$request = $this->getRequest();
	 	
	 	$institution = $this->getDoctrine()->getRepository('InstitutionBundle:Institution')->find($this->getRequest()->get('institutionId', null));
	 
	 	if (!$institution) {
	 		
	 		throw $this->createNotFoundException();
	 	}
	 	
	 	$form = $this->createForm(new InstitutionDetailType(), $institution, array('profile_type' => false, 'hidden_field' => false));
	 	 
	 	//update institution details
	 	if ($request->isMethod('POST')) {
	 	
	 		// Get contactNumbers and convert to json format
	 		$contactNumber = json_encode($request->get('contactNumber'));
	 		$websites = json_encode($request->get('websites'));
	 		
	 		$form->bindRequest($request);
	
	 		if ($form->isValid()) {
	 			
	 			$institution = $form->getData();
	 			
	 			$institution->setWebsites($websites);
	 			$institution->setContactNumber($contactNumber);
	 	
	 			$institution = $this->get('services.institution.factory')->save($institution);
	 			$this->get('session')->setFlash('notice', "Successfully updated account");
	 	
	 			//create event on editInstitution and dispatch
	 			$this->get('event_dispatcher')->dispatch(InstitutionBundleEvents::ON_EDIT_INSTITUTION, $this->get('events.factory')->create(InstitutionBundleEvents::ON_EDIT_INSTITUTION, $institution));
	 			
	 			return $this->redirect($this->generateUrl('institution_homepage'));
	 		}
	 	}

	 	return $this->render('InstitutionBundle:Institution:accountProfileForm.html.twig', array(
	 					'form' => $form->createView(),
	 					'institution' => $institution
	 	));
	 }
}