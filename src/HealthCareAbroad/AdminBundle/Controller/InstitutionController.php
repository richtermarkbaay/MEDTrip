<?php

namespace HealthCareAbroad\AdminBundle\Controller;


use HealthCareAbroad\InstitutionBundle\Form\InstitutionSignUpFormType;

use Symfony\Component\Validator\Constraints\Date;

use HealthCareAbroad\InstitutionBundle\Form\InstitutionMedicalCenterFormType;

use HealthCareAbroad\SubSpecializationBundle\Entity\Specialization;
use HealthCareAbroad\SubSpecializationBundle\Entity\SubSpecializationProcedure;

use HealthCareAbroad\InstitutionBundle\Entity\Institution;
use HealthCareAbroad\InstitutionBundle\Entity\InstitutionStatus;
use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter;
use HealthCareAbroad\InstitutionBundle\Entity\InstitutionSubSpecializationProcedure;
use HealthCareAbroad\InstitutionBundle\Entity\InstitutionSubSpecialization;
use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenterStatus;

use HealthCareAbroad\InstitutionBundle\Form\InstitutionMedicalCenterType;
use HealthCareAbroad\InstitutionBundle\Form\InstitutionSubSpecializationProcedureFormType;
use HealthCareAbroad\InstitutionBundle\Form\InstitutionSubSpecializationFormType;

use HealthCareAbroad\InstitutionBundle\Event\InstitutionBundleEvents;

use HealthCareAbroad\HelperBundle\Form\InstitutionFormType;
use HealthCareAbroad\UserBundle\Entity\SiteUser;
use HealthCareAbroad\InstitutionBundle\Event\CreateInstitutionEvent;
use HealthCareAbroad\InstitutionBundle\Event\InstitutionEvents;
use HealthCareAbroad\InstitutionBundle\Entity\InstitutionTypes;
use HealthCareAbroad\UserBundle\Entity\InstitutionUser;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use JMS\SecurityExtraBundle\Annotation\PreAuthorize;

class InstitutionController extends Controller
{    
    protected $institution;
    protected $institutionMedicalCenter;
    protected $institutionSpecialization;

    function preExecute() 
    {
        $request = $this->getRequest();
        // Check Institution
        if ($request->get('institutionId')) {
            $this->institution = $this->getDoctrine()->getRepository('InstitutionBundle:Institution')->find($request->get('institutionId'));
            
            if(!$this->institution) {
                throw $this->createNotFoundException('Invalid Institution');                
            }
        }

        $this->service = $this->get('services.institution_medical_center');
    }

    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_VIEW_INSTITUTIONS')")
     */
    public function indexAction()
    {
        $params = array(
            'pager' => $this->pager,
            'institutions' => $this->filteredResult, 
            //'statusList' => InstitutionStatus::getStatusList(),
            'statusList' => InstitutionStatus::getBitValueLabels(),
            'updateStatusOptions' => InstitutionStatus::getUpdateStatusOptions()
        );

        return $this->render('AdminBundle:Institution:index.html.twig', $params);
    }
    
    /**
     * Add new User and Institution
     * @author Chaztine Blance
     */
    public function addAction(Request $request){
    
    	$institutionType = $request->get('institutionType', InstitutionTypes::MEDICAL_GROUP_NETWORK_MEMBER);
    
    	$factory = $this->get('services.institution.factory');
    	$institution = $factory->createByType($institutionType);
    	
    	$form = $this->createForm(new InstitutionSignUpFormType(), $institution, array('include_terms_agreement' => false));
		
	    	if ($request->isMethod('POST')) {
	    		$form->bind($request);
	    		 
	    		if ($form->isValid()) {
	    	
	    			$institution = $form->getData();
	    	
	    			// initialize required database fields
	    			$institution->setAddress1('');
	    			$institution->setAddress2('');
	    			$institution->setContactEmail('');
	    			$institution->setContactNumber('');
	    			$institution->setLogo('');
	    			$institution->setCoordinates('');
	    			$institution->setState('');
	    			$institution->setWebsites('');
	    			$institution->setStatus(InstitutionStatus::getBitValueForActiveStatus());
	    			$institution->setZipCode('');
	    			$factory->save($institution);
	    			 
	    			// create Institution user
	    			$institutionUser = new InstitutionUser();
	    			$institutionUser->setEmail($form->get('email')->getData());
	    			$institutionUser->setFirstName($institution->getName());
	    			$institutionUser->setLastName('Admin');
	    			$institutionUser->setPassword($form->get('password')->getData());
	    			$institutionUser->setInstitution($institution);
	    			$institutionUser->setStatus(SiteUser::STATUS_ACTIVE);
	    			 
	    			// dispatch event
	    			$this->get('event_dispatcher')->dispatch(InstitutionBundleEvents::ON_ADD_INSTITUTION,
	    							$this->get('events.factory')->create(InstitutionBundleEvents::ON_ADD_INSTITUTION,$institution,array('institutionUser' => $institutionUser)
    							));
	    	
	    			$institutionId = $institution->getId();	 
	    	
	    			return $this->redirect($this->generateUrl('admin_institution_add_details', array('id' => $institutionId)));

	    		}
	    	}
  	
    	return $this->render('AdminBundle:Institution:add.html.twig', array(
    					'form' => $form->createView(),
    					'institutionTypes' => InstitutionTypes::getList(),
    					'selectedInstitutionType' => $institutionType,
    	));
    	 
    }
    
    /**
     * Add Institution Details
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addDetailsAction(Request $request){
    	
    	$id = $request->get('id', null);

    	$institution = $this->getDoctrine()->getRepository('InstitutionBundle:Institution')->find($id);
    	
	    $form = $this->createForm(new InstitutionFormType(), $institution);
	    	
	    return $this->render('AdminBundle:Institution:addDetails.html.twig', array(
				'form' => $form->createView(),
				'institution' => $institution,
	    		'id' => $id
    										
	    ));
    }
    
    
    /**
     * Save Institution Details
     */
    public function saveAction(){
    	
    	$request = $this->getRequest();
    	
    	$id = $request->get('id', null);
    	
    	$institution = $this->getDoctrine()->getRepository('InstitutionBundle:Institution')->find($id);
    	 
    	$form = $this->createForm(new InstitutionFormType(), $institution);
    	
    	//add institution details
    	if ($request->isMethod('POST')) {

    		$form->bindRequest($request);
    			
    		if ($form->isValid()) {

    			$institution = $this->get('services.institution.factory')->save($form->getData());
    			$this->get('session')->setFlash('notice', "Successfully added");
    			 
    			//create event on editInstitution and dispatch
    			$this->get('event_dispatcher')->dispatch(InstitutionBundleEvents::ON_EDIT_INSTITUTION, $this->get('events.factory')->create(InstitutionBundleEvents::ON_EDIT_INSTITUTION, $institution));
    			 
    		}
    	}

    	return $this->redirect($this->generateUrl('admin_institution_view', array('institutionId' => $id)));
    	 
    }
    
    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_INSTITUTION')")
     */
    public function viewAction(Request $request)
    {   
        return $this->render('AdminBundle:Institution:view.html.twig', array('institution' => $this->institution));
    }
    
    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_DELETE_INSTITUTION')")
     */
    public function updateStatusAction()
    {
        $request = $this->getRequest();

        if(!InstitutionStatus::isValid($request->get('status'))) {
            $request->getSession()->setFlash('error', 'Unable to update status. ' . $request->get('status') . ' is invalid status value!');

            return $this->redirect($this->generateUrl('admin_institution_index'));
        }

        $this->institution->setStatus(InstitutionStatus::getBitValue($request->get('status')));

        $em = $this->getDoctrine()->getEntityManager();
        $em->persist($this->institution);
        $em->flush($this->institution);

        // dispatch EDIT institution event
        $event = $this->get('events.factory')->create(InstitutionBundleEvents::ON_EDIT_INSTITUTION, $this->institution);
        $this->get('event_dispatcher')->dispatch(InstitutionBundleEvents::ON_EDIT_INSTITUTION, $event);

        $request->getSession()->setFlash('success', '"'.$this->institution->getName().'" has been updated!');

        return $this->redirect($this->generateUrl('admin_institution_index'));
    }
    
    /**
     *
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_INSTITUTION')")
     */
    public function updateMedicalCenterStatusAction()
    {
        $request = $this->getRequest();
        $status = $request->get('status');
    
        $redirectUrl = $this->generateUrl('admin_institution_manageCenters', array('institutionId' => $request->get('institutionId')));
    
        if(!InstitutionMedicalCenterStatus::isValid($status)) {
            $request->getSession()->setFlash('error', "Unable to update status. $status is invalid status value!");
    
            return $this->redirect($redirectUrl);
        }

        $this->institutionMedicalCenter->setStatus($status);
    
        $em = $this->getDoctrine()->getEntityManager();
        $em->persist($this->institutionMedicalCenter);
        $em->flush($this->institutionMedicalCenter);

        // dispatch EDIT institutionMedicalCenter event
        $actionEvent = InstitutionBundleEvents::ON_UPDATE_STATUS_INSTITUTION_MEDICAL_CENTER;
        $event = $this->get('events.factory')->create($actionEvent, $this->institutionMedicalCenter, array('institutionId' => $request->get('institutionId')));
        $this->get('event_dispatcher')->dispatch($actionEvent, $event);

        $request->getSession()->setFlash('success', '"'.$this->institutionMedicalCenter->getName().'" status has been updated!');

        return $this->redirect($redirectUrl);
    }
    
}