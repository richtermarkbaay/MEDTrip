<?php

/**
 * @author Alnie Jacobe
 * @author Chaztine Blance
 */

namespace HealthCareAbroad\InstitutionBundle\Controller;

use HealthCareAbroad\InstitutionBundle\Form\InstitutionUserFormType;

use HealthCareAbroad\UserBundle\Entity\InstitutionUser;

use HealthCareAbroad\InstitutionBundle\Event\InstitutionBundleEvents;

use HealthCareAbroad\InstitutionBundle\Event\EditInstitutionEvent;

use HealthCareAbroad\InstitutionBundle\Form\InstitutionDetailType;

use HealthCareAbroad\InstitutionBundle\Entity\Institution;

use HealthCareAbroad\AdminBundle\Entity\OfferedService;

use HealthCareAbroad\InstitutionBundle\Form\InstitutionOfferedServiceType;

use HealthCareAbroad\HelperBundle\Services\LocationService;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use ChromediaUtilities\Helpers\SecurityHelper;
use JMS\SecurityExtraBundle\Annotation\PreAuthorize;

class InstitutionController extends InstitutionAwareController
{
    
	/**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_INSTITUTIONS')")
     */
	public function editInstitutionAction()
	{
		$request = $this->getRequest();
		
		//render template      
		$form = $this->createForm(new InstitutionDetailType(), $this->institution);		
		$languages = $this->getDoctrine()->getRepository('AdminBundle:Language')->getActiveLanguages();
		
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
		
		$languageArr = array();
		
		foreach ($languages as $e) {
			
			$languageArr[] = array('value' => $e->getName(), 'id' => $e->getId());
		}
		
		$institutionLanguage = $this->getDoctrine()->getRepository('AdminBundle:Language')->getInstitutionLanguage($this->institution->getId());

 		return $this->render('InstitutionBundle:Institution:editInstitution.html.twig', array(
			'form' => $form->createView(),
			'institution' => $this->institution,
			'languagesJSON' => \json_encode($languageArr),
			'institutionLanguage' => $institutionLanguage
		));
	}
	public function viewAllStaffAction(Request $request)
	{
	    
	    $users = $this->get('services.institution')->getAllStaffOfInstitution($this->institution);
	    $userTypes = $this->getDoctrine()->getRepository('UserBundle:InstitutionUserType')->getAllEditable($this->institution);
	    //         echo $this->institution->getId();
	    $institutionUser = new InstitutionUser();
	    $form = $this->createForm(new InstitutionUserFormType(), $institutionUser);
	    return $this->render('InstitutionBundle:InstitutionUser:viewAll.html.twig', array(
	                    'users' => $users,
	                    'userTypes' => $userTypes,
	                    'form' => $form->createView()
	    ));
	}
	
	public function uploadLogoAction()
	{
	    if($this->getRequest()->files->get('logo')) {
	        $file = $this->getRequest()->files->get('logo');
	        $this->get('services.institution.media')->uploadLogo($file, $this->institution);
	    }

	    return $this->redirect($this->getRequest()->headers->get('referer'));
	}
	
	public function uploadFeaturedImageAction()
	{
	    if($this->getRequest()->files->get('featuredImage')) {
	        $file = $this->getRequest()->files->get('featuredImage');
	        $this->get('services.institution.media')->uploadFeaturedImage($file, $this->institution);
	    }
	    
	    return $this->redirect($this->getRequest()->headers->get('referer'));
	}
}