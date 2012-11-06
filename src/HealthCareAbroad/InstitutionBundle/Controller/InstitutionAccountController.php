<?php 
/*
 * @author Chaztine Blance
 */
namespace HealthCareAbroad\InstitutionBundle\Controller;

use HealthCareAbroad\InstitutionBundle\Event\InstitutionBundleEvents;
use HealthCareAbroad\InstitutionBundle\Event\EditInstitutionEvent;
use HealthCareAbroad\InstitutionBundle\Form\InstitutionProfileType;
use HealthCareAbroad\InstitutionBundle\Entity\Institution;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use ChromediaUtilities\Helpers\SecurityHelper;
use JMS\SecurityExtraBundle\Annotation\PreAuthorize;

class InstitutionAccountController extends Controller
{
	 public function accountAction(){

	 	$institutionId = $this->getRequest()->get('institutionId', null);
	 	
	 	$institution = $this->getDoctrine()->getRepository('InstitutionBundle:Institution')->find($institutionId);
	 	//render template

	 	$form = $this->createForm(new InstitutionProfileType(), $institution);

	 	return $this->render('InstitutionBundle:Institution:accountProfileForm.html.twig', array(
	 					'form' => $form->createView(),
	 					'institution' => $institution
	 	));
	 }
	 
	 public function saveAction(){
	 	
	 	$request = $this->getRequest();
	 	
	 	//update institution details
	 	if ($request->isMethod('POST')) {
	 		// Get contactNumbers and convert to json format
	 		$contactNumber = json_encode($request->get('contactNumber'));
	 	
	 		$form->bindRequest($request);
	 			
	 		if ($form->isValid()) {
	 			 
	 			// Set Contact Number before saving
	 			$form->getData()->setContactNumber($contactNumber);
	 			 
	 			$institution = $this->get('services.institution.factory')->save($form->getData());
	 			$this->get('session')->setFlash('notice', "Successfully updated account");
	 	
	 			//create event on editInstitution and dispatch
	 			$this->get('event_dispatcher')->dispatch(InstitutionBundleEvents::ON_EDIT_INSTITUTION, $this->get('events.factory')->create(InstitutionBundleEvents::ON_EDIT_INSTITUTION, $institution));
	 	
	 		}
	 	}
	 	
	 	
	 	return $this->render('InstitutionBundle:Institution:editInstitution.html.twig');
	 	
	 }
}