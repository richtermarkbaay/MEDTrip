<?php

/*
 * author Alnie Jacobe
 */

namespace HealthCareAbroad\InstitutionBundle\Controller;

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

	
class InstitutionController  extends InstitutionAwareController
{
	
	/**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_INSTITUTIONS')")
     */
	public function editInstitutionAction()
	{
		$request = $this->getRequest();
		
		$institutionId = $this->getRequest()->get('institutionId', null);
		
		if (!$institutionId){
			// no account id in parameter, editing currently logged in account
			$session = $this->getRequest()->getSession();
			$institutionId = $session->get('institutionId');
		}
		
		//render template      
		$form = $this->createForm(new InstitutionDetailType(), $this->institution);		
		$languages = $this->getDoctrine()->getRepository('AdminBundle:Language')->getActiveLanguages();
		
		//update institution details
		if ($request->isMethod('POST')) {
			$form->bindRequest($request);			
			if ($form->isValid()) {
				$institution = $this->get('services.institution')->updateInstitution($form->getData());
				$this->get('session')->setFlash('notice', "Successfully updated account");

				//create event on editInstitution and dispatch
				$this->get('event_dispatcher')->dispatch(InstitutionBundleEvents::ON_EDIT_INSTITUTION, $this->get('events.factory')->create(InstitutionBundleEvents::ON_EDIT_INSTITUTION, $institution));
			}
		}
		
		$languageArr = array();
		foreach ($languages as $e) {
			$languageArr[] = array('value' => $e->getName(), 'id' => $e->getId());
		}
		$institutionLanguage = $this->getDoctrine()->getRepository('AdminBundle:Language')->getInstitutionLanguage($institutionId);
		
 		return $this->render('InstitutionBundle:Institution:editInstitution.html.twig', array(
				'form' => $form->createView(),
				'institution' => $this->institution,
				'languagesJSON' => \json_encode($languageArr),
 				'institutionLanguage' => $institutionLanguage
		));
		
	}
	
	
	
}