<?php

/*
 * author Alnie Jacobe
 */

namespace HealthCareAbroad\InstitutionBundle\Controller;

use HealthCareAbroad\InstitutionBundle\Event\InstitutionBundleEvents;

use HealthCareAbroad\InstitutionBundle\Event\EditInstitutionEvent;
use HealthCareAbroad\InstitutionBundle\Event\InstitutionEvents;

use HealthCareAbroad\InstitutionBundle\Form\InstitutionDetailType;

use HealthCareAbroad\InstitutionBundle\Entity\Institution;

use HealthCareAbroad\HelperBundle\Services\LocationService;

use HealthCareAbroad\UserBundle\Entity\InstitutionUser;

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
		
		//render template      
		$form = $this->createForm(new InstitutionDetailType(), $this->institution);
		
		//update institution details
		if ($request->isMethod('POST')) {
			
			$form->bindRequest($request);
			
			if ($form->isValid()) {
				
				$institution = $this->get('services.institution')->updateInstitution($this->institution);
				$this->get('session')->setFlash('notice', "Successfully updated account");
				
				//create event on editInstitution and dispatch
				$event = new EditInstitutionEvent($institution);
				$this->get('event_dispatcher')->dispatch(InstitutionBundleEvents::ON_EDIT_INSTITUTION, $event);
			}
		}
		
		return $this->render('InstitutionBundle:Institution:editInstitution.html.twig', array(
				'form' => $form->createView(),
				'institution' => $this->institution
		));
		
	}
	
}
?>