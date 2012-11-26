<?php 
/*
 * @author Chaztine Blance
 * Create Profile after Sign up
 */
namespace HealthCareAbroad\InstitutionBundle\Controller;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionStatus;

use HealthCareAbroad\InstitutionBundle\Event\InstitutionBundleEvents;
use HealthCareAbroad\InstitutionBundle\Event\EditInstitutionEvent;
use HealthCareAbroad\InstitutionBundle\Form\InstitutionDetailType;
use HealthCareAbroad\InstitutionBundle\Entity\Institution;
use HealthCareAbroad\InstitutionBundle\Entity\InstitutionTypes;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use ChromediaUtilities\Helpers\SecurityHelper;
use JMS\SecurityExtraBundle\Annotation\PreAuthorize;
use Symfony\Component\Security\Core\SecurityContext;


class InstitutionAccountController extends Controller
{
	protected $institution;
	
	function preExecute()
	{
		$request = $this->getRequest();
		$session = $this->getRequest()->getSession();
		 
		// Check Institution
		if ($session->get('institutionId')) {
	
			$this->institution = $this->getDoctrine()->getRepository('InstitutionBundle:Institution')->find($session->get('institutionId'));
			
			if(!$this->institution) {
				throw $this->createNotFoundException('Invalid Institution');
			}
		}
	}
	 
	 public function accountAction(Request $request){
	
	 	if ($this->institution->getStatus() != InstitutionStatus::getBitValueForInactiveStatus()) {
	 		 
	 		return $this->redirect($this->generateUrl('institution_homepage'));
	 	}
	
	 	$form = $this->createForm(new InstitutionDetailType(), $this->institution, array('profile_type' => false, 'hidden_field' => false));
	 	 
	 	//update institution details
	 	if ($request->isMethod('POST')) {
	 	
	 		// Get contactNumbers and convert to json format
	 		$contactNumber = json_encode($request->get('contactNumber'));
	 		$websites = json_encode($request->get('websites'));
	 		
	 		$form->bindRequest($request);
	
	 		if ($form->isValid()) {

	 			$this->institution = $form->getData();
	 			
	 			$this->institution->setWebsites($websites);
	 			$this->institution->setContactNumber($contactNumber);
	 			$this->institution->setStatus(InstitutionStatus::getBitValueForUnapprovedStatus());

	 			$institution = $this->get('services.institution.factory')->save($this->institution);
	 			$this->get('session')->setFlash('notice', "Successfully updated account");
	 	
	 			//create event on editInstitution and dispatch
	 			$this->get('event_dispatcher')->dispatch(InstitutionBundleEvents::ON_EDIT_INSTITUTION, $this->get('events.factory')->create(InstitutionBundleEvents::ON_EDIT_INSTITUTION, $this->institution));
	 			
	 			return $this->redirect($this->generateUrl('institution_homepage'));
	 		}
	 	}
	 	

	 	return $this->render('InstitutionBundle:Institution:accountProfileForm.html.twig', array(
	 					'form' => $form->createView(),
	 					'institution' => $this->institution
	 	));
	 }
}