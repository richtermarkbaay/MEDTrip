<?php 
/*
 * @author Chaztine Blance
 * Create Profile after Sign up
 */
namespace HealthCareAbroad\InstitutionBundle\Controller;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter;

use HealthCareAbroad\InstitutionBundle\Form\InstitutionProfileFormType;

use HealthCareAbroad\InstitutionBundle\Services\InstitutionService;

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


class InstitutionAccountController extends InstitutionAwareController
{
    /**
     * @var InstitutionService
     */
    protected $institutionService;
    
    /**
     * @var Request
     */
    protected $request;
    
	public function preExecute()
	{
	    $this->institutionService = $this->get('services.institution');
	    $this->request = $this->getRequest();
	}
	
	/**
	 * Landing page after signing up as an Institution. Logic will differ depending on the type of institution
	 * 
	 * @param Request $request
	 */
    public function afterRegistrationLandingAction(Request $request)
    {
        switch ($this->institution->getType())
        {
            case InstitutionTypes::SINGLE_CENTER:
                $response = $this->completeRegistrationSingleCenter();
                break;
            case InstitutionTypes::MULTIPLE_CENTER:
                break;
            case InstitutionTypes::MEDICAL_TOURISM_FACILITATOR:
            default:
                $response = $this->completeRegistrationMultipleCenter();
                break;
        }

        return $response;
    }
    
    /**
     * This is the action handler after signing up as an Institution with Single Center.
     * User will be directed immediately to create clinic page.
     * 
     * TODO:
     *     This has a crappy rule where institution name and description will internally be the name and description of the clinic.
     *     
     * @author acgvelarde    
     * @return
     */
    protected function completeRegistrationSingleCenter()
    {
        $form = $this->createForm(new InstitutionProfileFormType(), $this->institution);
        $institutionMedicalCenter = new InstitutionMedicalCenter();
        
        if ($this->request->isMethod('POST')) {
            $form->bind($this->request);
            
            if ($form->isValid()) {
                
                $this->institution = $form->getData();
                
                // save institution
                $this->get('services.institution.factory')->save($form->getData());
                
                // also set the name and description of the medical center
                $institutionMedicalCenter->setName($this->institution->getName());
                $institutionMedicalCenter->setDescription($this->institution->getDescription());
                $institutionMedicalCenter->setInstitution($this->institution);
                
                // TODO: do logic for saving the business hours
                $institutionMedicalCenter->setBusinessHours('');
                
                // save institution medical center as draft
                $this->get('services.institution_medical_center')->saveAsDraft($institutionMedicalCenter);
                
                return $this->redirect($this->generateUrl('institution_homepage'));
            }
        }
        
        return $this->render('InstitutionBundle:Institution:afterRegistration.singleCenter.html.twig', array(
            'form' => $form->createView(),
            'institutionMedicalCenter' => $institutionMedicalCenter
        ));
    }
    
    /**
     * 
     */
    protected function completeRegistrationMultipleCenter()
    {
        return $this->render($view);
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