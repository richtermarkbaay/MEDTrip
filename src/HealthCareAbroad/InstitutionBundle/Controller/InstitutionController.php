<?php
namespace HealthCareAbroad\InstitutionBundle\Controller;

use HealthCareAbroad\InstitutionBundle\Event\CreateInstitutionInvitationEvent;
use HealthCareAbroad\InstitutionBundle\Event\InstitutionInvitationEvents;
use HealthCareAbroad\InstitutionBundle\Event\CreateInstitutionEvent;
use HealthCareAbroad\InstitutionBundle\Event\InstitutionEvents;


use HealthCareAbroad\InstitutionBundle\Form\InstitutionType;
use HealthCareAbroad\InstitutionBundle\Form\InstitutionDetailType;
use HealthCareAbroad\InstitutionBundle\Form\InstitutionInvitationType;

use HealthCareAbroad\InstitutionBundle\Entity\Institution;
use HealthCareAbroad\InstitutionBundle\Entity\InstitutionInvitation;

use HealthCareAbroad\HelperBundle\Entity\Country;

use HealthCareAbroad\HelperBundle\Entity\InvitationToken;
use HealthCareAbroad\HelperBundle\Services\LocationService;

use HealthCareAbroad\UserBundle\Entity\InstitutionUser;
use HealthCareAbroad\UserBundle\Entity\SiteUser;

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
		//render data to template      
		$form = $this->createForm(new InstitutionDetailType(), $this->institution);
		
		//update institution details
		if ($this->getRequest()->isMethod('POST')) {
			
			$form->bindRequest($this->getRequest());
			
			if ($form->isValid()) {
				
				$institution = $this->get('services.institution')->updateInstitution($this->institution);
				$this->get('session')->setFlash('notice', "Successfully updated account");
				//create event on editInstitution and dispatch
				$event = new CreateInstitutionEvent($institution);
				$this->get('event_dispatcher')->dispatch(InstitutionEvents::ON_EDIT_INSTITUTION, $event);
			}
		}
		return $this->render('InstitutionBundle:Institution:editInstitution.html.twig', array(
				'form' => $form->createView(),
				'institution' => $this->institution
		));
		
	}
	
}
?>