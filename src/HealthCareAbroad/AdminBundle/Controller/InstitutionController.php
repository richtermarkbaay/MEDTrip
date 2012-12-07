<?php

namespace HealthCareAbroad\AdminBundle\Controller;


use HealthCareAbroad\InstitutionBundle\Form\ListType\InstitutionOfferedServiceListType;

use HealthCareAbroad\AdminBundle\Entity\Language;

use HealthCareAbroad\AdminBundle\AdminBundle;

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

use HealthCareAbroad\InstitutionBundle\Form\InstitutionLanguageSpokenFormType;
use HealthCareAbroad\InstitutionBundle\Form\InstitutionOfferedServicesFormType;
use HealthCareAbroad\InstitutionBundle\Form\InstitutionDetailType;
use HealthCareAbroad\UserBundle\Entity\SiteUser;
use HealthCareAbroad\InstitutionBundle\Event\CreateInstitutionEvent;
use HealthCareAbroad\InstitutionBundle\Event\EditInstitutionEvent;
use HealthCareAbroad\InstitutionBundle\Event\InstitutionEvents;
use HealthCareAbroad\InstitutionBundle\Entity\InstitutionTypes;
use HealthCareAbroad\UserBundle\Entity\InstitutionUser;
use HealthCareAbroad\HelperBundle\Services\LocationService;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use ChromediaUtilities\Helpers\SecurityHelper;
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
            
            //$this->institution = $this->getDoctrine()->getRepository('InstitutionBundle:Institution')->find($request->get('institutionId'));
            $this->institution = $this->get('services.institution.factory')->findById($request->get('institutionId'));
       
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
    
    	$institutionType = $request->get('institutionType', InstitutionTypes::MULTIPLE_CENTER);   
    	$factory = $this->get('services.institution.factory');
    	$institution = $factory->createInstance($institutionType);  	
    	$form = $this->createForm(new InstitutionSignUpFormType(), $institution, array('include_terms_agreement' => false));
		
	    	if ($request->isMethod('POST')) {
	    		$form->bind($request);
	    		 
	    		if ($form->isValid()) {
	    	
	    			$institution = $form->getData();
	    	
	    			// initialize required database fields
	    			$institution->setAddress1('');
	    			$institution->setContactEmail('');
	    			$institution->setContactNumber('');
	    			$institution->setDescription('');
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
	    	
	    			return $this->redirect($this->generateUrl('admin_institution_add_details', array('institutionId' => $institution->getId())));
	    		}
	    	}
	    	
    	return $this->render('AdminBundle:Institution:add.html.twig', array(
    					'form' => $form->createView(),
    					'institutionTypes' => InstitutionTypes::getFormChoices(),
    					'selectedInstitutionType' => $institutionType,
    	));
    }
    
    /**
     * Add Institution Details
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addDetailsAction(Request $request){
       
	    $form = $this->createForm(new InstitutionDetailType(), $this->institution, array('profile_type' => false));

	    //redirect to edit institution field if not newly added institution
	    
	    if($this->institution->getContactEmail()){
	    	
	    	return $this->redirect($this->generateUrl('admin_institution_edit', array('institutionId' => $this->institution->getId())));
	    }
	    
	    if ($request->isMethod('POST')) {
	    		
	    	$form->bindRequest($request);
	    
	    	if ($form->isValid()) {

	    		$institution = $this->get('services.institution.factory')->save($form->getData());
	    		$this->get('session')->setFlash('notice', "Successfully updated account");
	    
	    		//create event on editInstitution and dispatch
	    		$this->get('event_dispatcher')->dispatch(InstitutionBundleEvents::ON_EDIT_INSTITUTION, $this->get('events.factory')->create(InstitutionBundleEvents::ON_EDIT_INSTITUTION, $institution));
	    
	    		return $this->redirect($this->generateUrl('admin_institution_edit', array('institutionId' => $this->institution->getId())));
	    	}
	    }
	    
	    return $this->render('AdminBundle:Institution:addDetails.html.twig', array(
				'form' => $form->createView(),
				'institution' => $this->institution,
	    		'id' => $this->institution->getId()
    										
	    ));
    }
    
    /*
     * Edit Institution Details
     */
    public function editDetailsAction(Request $request){
    
    	$form = $this->createForm(new InstitutionDetailType(), $this->institution);
    
    	if ($request->isMethod('POST')) {
    	    
    		
    	    $contactNumber = json_encode($request->get('contactNumber'));
    		$websites = json_encode($request->get('websites'));
    		 
    		$form->bindRequest($request);
    		
    		if ($form->isValid()) {
    			
    			$this->institution = $form->getData();
    			
    			$this->institution->getCity()->setCountry($this->institution->getCountry());
    			
    				
    			$this->institution->setWebsites($websites);
    			$this->institution->setContactNumber($contactNumber);
    
    			$institution = $this->get('services.institution.factory')->save($this->institution);
    			$this->get('session')->setFlash('notice', "Successfully updated account");
    			 
    			//create event on editInstitution and dispatch
    			$this->get('event_dispatcher')->dispatch(InstitutionBundleEvents::ON_EDIT_INSTITUTION, $this->get('events.factory')->create(InstitutionBundleEvents::ON_EDIT_INSTITUTION, $institution));
    			
    			return $this->redirect($this->generateUrl('admin_institution_manageCenters', array('institutionId' => $this->institution->getId())));
    		}
    	}
    	
    	//Check if there is an area code in Contact Number values
    	$json = '[{"area":"","number":"","type":"phone"}]';
    	$a1 = json_decode( $this->institution->getContactNumber(), true );
    	$contactNumbers = array();
    	
    	if ($a1){
    	    $keys = array_keys($a1[0]);
    	     
    	    if ($keys[0] == 'area'){
    	        $contactNumbers = $a1;
    	    }
    	    else{
    	        $contactNumbers = json_decode( $json, true );
    	    }    
    	}
   
    	return $this->render('AdminBundle:Institution:editDetails.html.twig', array(
			'form' => $form->createView(),
			'institution' => $this->institution,
			'arrContactNumber' => $contactNumbers,
			'id' => $this->institution->getId()
    	));
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
    
    /*
     * @author Chaztine Blance
     * Add Institution Language Spoken
     */
    public function addInstitutionLanguagesSpokenAction(Request $request)
    {
    	$languages = $this->getDoctrine()->getRepository('AdminBundle:Language')->getActiveLanguages();
    	$form = $this->createForm(new InstitutionLanguageSpokenFormType(),$this->institution);
 
    	if ($request->isMethod('POST')) {
    
    		$form->bind($request);
    		if ($form->isValid()) {
    
    			$institution = $this->get('services.institution.factory')->save($form->getData());
   				$this->get('session')->setFlash('notice', "Successfully updated Languages Spoken");
    
   				//create event on editInstitution and dispatch
   				$this->get('event_dispatcher')->dispatch(InstitutionBundleEvents::ON_EDIT_INSTITUTION, $this->get('events.factory')->create(InstitutionBundleEvents::ON_EDIT_INSTITUTION, $institution));
   				return $this->redirect($this->generateUrl('admin_institution_edit', array('institutionId' => $this->institution->getId())));
    		}
    	}
    	
    	$languageArr = array();
    	foreach ($languages as $e) {
    		
   			$languageArr[] = array('value' => $e->getName(), 'id' => $e->getId());
   		}
    		
    	$institutionLanguage = $this->getDoctrine()->getRepository('AdminBundle:Language')->getInstitutionLanguage($this->institution->getId());
   
    	return $this->render('AdminBundle:Institution:addLanguage.html.twig', array(
    			'form' => $form->createView(),
    			'institution' => $this->institution,
    			'languagesJSON' => \json_encode($languageArr),
    			'institutionLanguage' => $institutionLanguage,
    			'newObject' => true
    	));
   	}
   	
   	/*
   	 * Add Instiution Offered Services
   	 */
   	public function addInstitutionOfferedServicesAction(Request $request)
   	{
    		$form = $this->createForm(new InstitutionOfferedServicesFormType(),$this->institution);
   	
//    		if ($request->isMethod('POST')) {
   	
//    			$form->bind($request);
//    			if ($form->isValid()) {
   	
//    				$institution = $this->get('services.institution.factory')->save($form->getData());
//    				$this->get('session')->setFlash('notice', "Successfully updated Offered Services");
   	
//    				//create event on editInstitution and dispatch
//    				$this->get('event_dispatcher')->dispatch(InstitutionBundleEvents::ON_EDIT_INSTITUTION, $this->get('events.factory')->create(InstitutionBundleEvents::ON_EDIT_INSTITUTION, $institution));
   				
//    				return $this->redirect($this->generateUrl('admin_institution_edit', array('institutionId' => $this->institution->getId())));
//    			}
//    		}
//    		var_dump($form);exit;
//    		return $this->render('AdminBundle:Institution:addOfferedServices.html.twig', array(
//    						'form' => $form->createView(),
//    						'institution' => $this->institution,
//    						'newObject' => true
//    		));
   	    $form = $this->get('services.institution_property.formFactory')->buildFormByInstitutionPropertyTypeName($this->institution, 'ancilliary_service_id');
   	    $formActionUrl = $this->generateUrl('admin_institution_addAncilliaryService', array('institutionId' => $this->institution->getId()));
   	    if ($request->isMethod('POST')) {
   	        $form->bind($request);
   	        if ($form->isValid()) {
   	            $this->get('services.institution_property')->save($form->getData());
   	    
   	            return $this->redirect($formActionUrl);
   	        }
   	    }
   	    
   	    $params = array(
   	                    'formAction' => $formActionUrl,
   	                    'form' => $form->createView()
   	    );
   	    return $this->render('AdminBundle:InstitutionProperties:common.form.html.twig', $params);
   	}
}