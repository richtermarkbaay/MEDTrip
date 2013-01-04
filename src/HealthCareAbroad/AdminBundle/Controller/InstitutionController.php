<?php

namespace HealthCareAbroad\AdminBundle\Controller;

use HealthCareAbroad\InstitutionBundle\Entity\MedicalProviderGroup;

use HealthCareAbroad\HelperBundle\Classes\QueryOption;

use HealthCareAbroad\HelperBundle\Classes\QueryOptionBag;

use HealthCareAbroad\InstitutionBundle\Form\InstitutionProfileFormType;

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

use HealthCareAbroad\UserBundle\Entity\SiteUser;
use HealthCareAbroad\InstitutionBundle\Event\CreateInstitutionEvent;
use HealthCareAbroad\InstitutionBundle\Event\EditInstitutionEvent;
use HealthCareAbroad\InstitutionBundle\Event\InstitutionEvents;
use HealthCareAbroad\InstitutionBundle\Entity\InstitutionTypes;
use HealthCareAbroad\UserBundle\Entity\InstitutionUser;
use HealthCareAbroad\HelperBundle\Services\LocationService;
use HealthCareAbroad\InstitutionBundle\Services\InstitutionService;
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
        $medicalProviderGroup = $this->getDoctrine()->getRepository('InstitutionBundle:MedicalProviderGroup')->getActiveMedicalGroups();
    	$institutionType = $request->get('institution', InstitutionTypes::MULTIPLE_CENTER);   
  
    	$factory = $this->get('services.institution.factory');
    	$institution = $factory->createInstance($institutionType);  	
    	$form = $this->createForm(new InstitutionSignUpFormType(), $institution, array('include_terms_agreement' => false));
    
	    	if ($request->isMethod('POST')) {
	    	    $type= $_POST['institution'];
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
	    			$institution->setType($type['type']);
	    			$institution->setWebsites('');
	    			$institution->setStatus(InstitutionStatus::getBitValueForInactiveStatus());
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
	    	
	    	$medicalProviderGroupArr = array();
	    	
	    	foreach ($medicalProviderGroup as $e) {
	    	    $medicalProviderGroupArr[] = array('value' => $e->getName(), 'id' => $e->getId());
	    	}

    	return $this->render('AdminBundle:Institution:add.html.twig', array(
    					'form' => $form->createView(),
    					'institutionTypes' => InstitutionTypes::getFormChoices(),
    					'selectedInstitutionType' => $institutionType,
    	                'medicalProvidersJSON' => \json_encode($medicalProviderGroupArr)
    	));
    }
    
    /**
     * Add Institution Details, step 2 when creating new institution
     * 
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @author Chaztine Blance
     */
    public function addDetailsAction(Request $request){
       
	    $form = $this->createForm(new InstitutionProfileFormType(), $this->institution, array(InstitutionProfileFormType::OPTION_HIDDEN_FIELDS => array('name')));

	    // redirect to edit institution if status is already active
	    
	    if($this->get('services.institution')->isActive($this->institution)){
	        
	        return $this->redirect($this->generateUrl('admin_institution_edit', array('institutionId' => $this->institution->getId())));
	    }
	    
	    if ($request->isMethod('POST')) {
	        
	        $form->bindRequest($request);
	        
	        if ($form->isValid()) {
	            $institution = $form->getData();
	            
	            // update to active status
	            $institution->setStatus(InstitutionStatus::getBitValueForActiveStatus());
	            $this->get('services.institution.factory')->save($institution);
	    		$this->get('session')->setFlash('notice', "Successfully completed details of {$institution->getName()}.");
	    
	    		return $this->redirect($this->generateUrl('admin_institution_edit', array('institutionId' => $this->institution->getId())));
	    	}
	    }
	    
	    return $this->render('AdminBundle:Institution:addDetails.html.twig', array(
				'form' => $form->createView(),
				'institution' => $this->institution,
	    		'id' => $this->institution->getId()
    										
	    ));
    }
    
    /**
     * Edit Institution Details
     */
    public function editDetailsAction(Request $request){

        // TODO - Need to verify? Temporarily removed OPTION_HIDDEN_FIELDS 'name'
    	$form = $this->createForm(new InstitutionProfileFormType(), $this->institution, array(InstitutionProfileFormType::OPTION_HIDDEN_FIELDS => array('')));

    	if ($request->isMethod('POST')) {
    		$form->bindRequest($request);
    		if ($form->isValid()) {
    			
    			$this->institution = $form->getData();    
    			$institution = $this->get('services.institution.factory')->save($this->institution);
    			$this->get('session')->setFlash('notice', "Successfully updated account");
    			 
    			//create event on editInstitution and dispatch
    			$this->get('event_dispatcher')->dispatch(InstitutionBundleEvents::ON_EDIT_INSTITUTION, $this->get('events.factory')->create(InstitutionBundleEvents::ON_EDIT_INSTITUTION, $institution));
    			
    			return $this->redirect($this->generateUrl('admin_institution_manageCenters', array('institutionId' => $this->institution->getId())));
    		}
    	}
   
    	return $this->render('AdminBundle:Institution:editDetails.html.twig', array(
			'form' => $form->createView(),
			'institution' => $this->institution,
			'id' => $this->institution->getId()
    	));
    }
    
    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_INSTITUTION')")
     */
    public function viewAction(Request $request)
    {   
        $institutionService = $this->get('services.institution');
        $recentMedicalCenters = $this->get('services.institution')->getRecentlyAddedMedicalCenters($this->institution, new QueryOptionBag(array(QueryOption::LIMIT => 1)));
        
        return $this->render('AdminBundle:Institution:view.html.twig', array(
            'recentMedicalCenters' => $recentMedicalCenters,
            'institution' => $this->institution,
            'isSingleCenter' => $institutionService->isSingleCenter($this->institution)
        ));
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
   	
   	public function addInstitutionOfferedServicesAction(Request $request)
   	{
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