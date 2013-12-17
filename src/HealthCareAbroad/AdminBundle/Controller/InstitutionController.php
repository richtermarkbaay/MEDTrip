<?php

namespace HealthCareAbroad\AdminBundle\Controller;

use HealthCareAbroad\FrontendBundle\Services\FrontendMemcacheKeysHelper;

use Symfony\Component\EventDispatcher\GenericEvent;

use HealthCareAbroad\InstitutionBundle\Event\InstitutionEvent;

use HealthCareAbroad\InstitutionBundle\Form\InstitutionUserSignUpFormType;

use HealthCareAbroad\HelperBundle\Entity\ContactDetailTypes;

use HealthCareAbroad\HelperBundle\Entity\ContactDetail;

use HealthCareAbroad\InstitutionBundle\Services\InstitutionMediaService;

use HealthCareAbroad\InstitutionBundle\Entity\MedicalProviderGroup;

use HealthCareAbroad\HelperBundle\Classes\QueryOption;

use HealthCareAbroad\HelperBundle\Classes\QueryOptionBag;

use HealthCareAbroad\AdminBundle\Form\InstitutionProfileFormType;

use HealthCareAbroad\InstitutionBundle\Form\ListType\InstitutionOfferedServiceListType;

use HealthCareAbroad\AdminBundle\Entity\Language;

use HealthCareAbroad\AdminBundle\AdminBundle;

use HealthCareAbroad\InstitutionBundle\Form\InstitutionSignUpFormType;

use Symfony\Component\Validator\Constraints\Date;

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
use HealthCareAbroad\PagerBundle\Pager;
use HealthCareAbroad\PagerBundle\Adapter\DoctrineOrmAdapter;
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

use HealthCareAbroad\PagerBundle\Adapter\ArrayAdapter;

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
    public function institutionsContactInfoAction()
    {
        $params = array('institutions' => $this->filteredResult, 'statuses' => InstitutionStatus::getBitValueLabels());

        return $this->render('AdminBundle:Institution:institutionsContactInfo.html.twig', $params);
    }

    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_VIEW_INSTITUTIONS')")
     */
    public function getAjaxAccountDataAction()
    {
        $result = array('error' => 'invalid account id!');
        
        if($accountId = $this->getRequest()->get('accountId')) {
            $result = $this->get('services.institutionUser')->getAccountDataById($accountId);            
            unset($result['password']);
        }

        return new Response(json_encode($result), 200, array('content-type' => 'application/json'));
    }

    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_VIEW_INSTITUTIONS')")
     */
    public function indexAction()
    {
        $institutionStatusForm = $this->createForm(new InstitutionProfileFormType(), new Institution());
        $params = array(
            'pager' => $this->pager,
            'institutions' => $this->filteredResult, 
            'statusList' => InstitutionStatus::getBitValueLabels(),
            'updateStatusOptions' => InstitutionStatus::getBitValueForActiveStatus(),
            'institutionStatusForm' =>$institutionStatusForm->createView()
        );

        return $this->render('AdminBundle:Institution:index.html.twig', $params);
    }
    
    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_VIEW_INSTITUTIONS')")
     */
    public function viewNewlyRegisteredAction()
    {
        $criteria = array('isFromInternalAdmin' => null, 'status' => InstitutionStatus::INACTIVE);
        $institutions = $this->getDoctrine()->getRepository('InstitutionBundle:Institution')->findBy($criteria);

        $params = array('institutions' => $institutions, 'statusList' => InstitutionStatus::getBitValueLabels());

        return $this->render('AdminBundle:Institution:newlyRegistered.html.twig', $params);
    }

    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_VIEW_INSTITUTIONS')")
     */
    public function recentlyApprovedAction()
    {
        $recentlyApprovedInstitutions = $this->get('services.recentlyApprovedListing')->getRecentlyApprovedInstitutions();

        $params = array('recentlyApprovedInstitutions' => $recentlyApprovedInstitutions);
    
        return $this->render('AdminBundle:Institution:recentlyApproved.html.twig', $params);
    }

    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_VIEW_INSTITUTIONS')")
     */
    public function recentlyApprovedClinicsAction()
    {
        $recentlyApprovedListings = $this->get('services.recentlyApprovedListing')->getRecentlyApprovedInstitutionMedicalCenters();
    
        $params = array('recentlyApprovedListings' => $recentlyApprovedListings);
    
        return $this->render('AdminBundle:Institution:recentlyApprovedClinics.html.twig', $params);
    }

    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_VIEW_INSTITUTIONS')")
     */
    public function viewNewlyAddedClinicsAction()
    {
        $criteria = array('isFromInternalAdmin' => null, 'status' => InstitutionMedicalCenterStatus::DRAFT);
        $centers = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionMedicalCenter')->findBy($criteria);
        
        
        $params = array('institutionMedicalCenters' => $centers, 'statusList' => InstitutionMedicalCenterStatus::getStatusList());

        return $this->render('AdminBundle:Institution:newlyAddedClinics.html.twig', $params);
    }
    
    /**
     * Add new User and Institution
     * @author Chaztine Blance
     */
    public function addAction(Request $request){
    	$factory = $this->get('services.institution.factory');
    	$institution = $factory->createInstance();  	
    	$institutionUser = new InstitutionUser();
    	
    	$this->get('services.contact_detail')->initializeContactDetails($institutionUser, array(ContactDetailTypes::PHONE, ContactDetailTypes::MOBILE));
    	
    	$form = $this->createForm(new InstitutionUserSignUpFormType(), $institutionUser, array('include_terms_agreement' => false));
	    	if ($request->isMethod('POST')) {
	    		$form->bind($request);
	    		 
	    		if ($form->isValid()) {
	    		    $postData = $request->get('institutionUserSignUp');
	    		    $institutionUser = $form->getData();
	    			// initialize required database fields
	    		    $institution->setName(uniqid());
	    			$institution->setAddress1('');
	    			$institution->setContactEmail('');
	    			$institution->setContactNumber('');
	    			$institution->setDescription('');
	    			$institution->setLogo(null);
	    			$institution->setCoordinates('');
	    			$institution->setType(trim($postData['type'])); /* FIX ME! */
	    			$institution->setWebsites('');
	    			$institution->setStatus(InstitutionStatus::getBitValueForInactiveStatus());
	    			$institution->setZipCode('');
	    			$institution->setSignupStepStatus(0);

	    			// Temporary Code to mark a newly added institution as added internally.
	    			// Added By: Adelbert Silla
	    			$institution->setIsFromInternalAdmin(1);
	    			
	    			
	    			$factory->save($institution);
	    			 
	    			// create Institution user
	    			$institutionUser = new InstitutionUser();
	    			$institutionUser->setEmail($form->get('email')->getData());
	    			$institutionUser->setFirstName($institution->getName());
	    			$institutionUser->setLastName('Admin');
	    			$institutionUser->setPassword(SecurityHelper::hash_sha256($form->get('password')->getData()));
	    			$institutionUser->setInstitution($institution);
	    			$institutionUser->setStatus(SiteUser::STATUS_ACTIVE);
	    			$this->get('services.contact_detail')->removeInvalidContactDetails($institutionUser);
//                     var_dump($institutionUser->getContactDetails()); exit;
	    			// dispatch event
	    			$this->get('event_dispatcher')->dispatch(InstitutionBundleEvents::ON_ADD_INSTITUTION,
	    							$this->get('events.factory')->create(InstitutionBundleEvents::ON_ADD_INSTITUTION,$institution,array('institutionUser' => $institutionUser)
    							));
	    	
	    			return $this->redirect($this->generateUrl('admin_institution_add_details', array('institutionId' => $institution->getId())));
	    		}
	    		else {
	    		    
	    		}
	    	}
	    	
    	return $this->render('AdminBundle:Institution:add.html.twig', array(
    					'form' => $form->createView(),
    					'institutionTypes' => InstitutionTypes::getFormChoices(),
    	));
    }
    
    /**
     * Add Institution Details, step 2 when creating new institution
     * 
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @author Chaztine Blance
     */
    public function addDetailsAction(Request $request)
    {
        $medicalProviderGroup = $this->getDoctrine()->getRepository('InstitutionBundle:MedicalProviderGroup')->getActiveMedicalGroups();
        $medicalProviderGroupArr = array();
        
        foreach ($medicalProviderGroup as $e) {
            $medicalProviderGroupArr[] = array('value' => $e->getName(), 'id' => $e->getId());
        }
        $this->get('services.contact_detail')->initializeContactDetails($this->institution, array(ContactDetailTypes::PHONE));

        $this->institution->setName(''); //set institution name to empty
	    // redirect to edit institution if status is already active
	    
	    if($this->get('services.institution')->isActive($this->institution)){
	        
	        return $this->redirect($this->generateUrl('admin_institution_edit_details', array('institutionId' => $this->institution->getId())));
	    }
	    if ($request->isMethod('GET')) {
	           $form = $this->createForm(new InstitutionProfileFormType(), $this->institution);
	    }
	    if ($request->isMethod('POST')) {
	        
	        $formVariables = $request->get(InstitutionProfileFormType::NAME);
	        unset($formVariables['_token']);
	        $removedFields = \array_diff(InstitutionProfileFormType::getFieldNames(), array_keys($formVariables));
	        
	        $form = $this->createForm(new InstitutionProfileFormType(), $this->institution,
                array( InstitutionProfileFormType::OPTION_HIDDEN_FIELDS => array(''), 
                       InstitutionProfileFormType::OPTION_BUBBLE_ALL_ERRORS => false, 
                       InstitutionProfileFormType::OPTION_REMOVED_FIELDS => $removedFields));
	        $formRequestData = $request->get($form->getName());
	        if (isset($formRequestData['medicalProviderGroups']) ) {
	            // we always expect 1 medical provider group
	            // if it is empty remove it from the array
	            if (isset($formRequestData['medicalProviderGroups'][0]) && '' == trim($formRequestData['medicalProviderGroups'][0]) ) {
	                unset($formRequestData['medicalProviderGroups'][0]);
	            }
	        }
	        
	        $form->bind($formRequestData);
	        
	        if ($form->isValid()) {
	            $institution = $form->getData();
	            
	            // update the sign up step status
	            $institution->setSignupStepStatus(0);
	            
	            // update to active status
	            $institution->setStatus(InstitutionStatus::getBitValueForActiveStatus());
	            $this->get('services.contact_detail')->removeInvalidContactDetails($institution);
	            $this->get('services.institution.factory')->save($institution);
	    		$this->get('session')->setFlash('notice', "Successfully completed details of {$institution->getName()}.");
	    
	    		return $this->redirect($this->generateUrl('admin_institution_view', array('institutionId' => $this->institution->getId())));
	    	}
	    }
	    
	    return $this->render('AdminBundle:Institution:addDetails.html.twig', array(
				'form' => $form->createView(),
				'institution' => $this->institution,
	    		'id' => $this->institution->getId(),
                'medicalProvidersJSON' => \json_encode($medicalProviderGroupArr)
	    ));
    }
    
    /**
     * Edit Institution Details
     */
    public function editDetailsAction(Request $request)
    {
        $error = false;
        $errorArr = array();
        $medicalProviderGroup = $this->getDoctrine()->getRepository('InstitutionBundle:MedicalProviderGroup')->getActiveMedicalGroups();
        $medicalProviderGroupArr = array();
        
        foreach ($medicalProviderGroup as $e) {
            $medicalProviderGroupArr[] = array('value' => $e->getName(), 'id' => $e->getId());
        }
        
        $this->get('services.contact_detail')->initializeContactDetails($this->institution, array(ContactDetailTypes::PHONE), $this->institution->getCountry());

        if ($request->isMethod('GET')) {
            $form = $this->createForm(new InstitutionProfileFormType(), $this->institution, array(InstitutionProfileFormType::OPTION_HIDDEN_FIELDS => array('') , InstitutionProfileFormType::OPTION_BUBBLE_ALL_ERRORS => false ));
        }
    	if ($request->isMethod('POST')) {
    	    
    	    $formVariables = $request->get(InstitutionProfileFormType::NAME);
    	    unset($formVariables['_token']);
    	    $removedFields = \array_diff(InstitutionProfileFormType::getFieldNames(), array_keys($formVariables));
    	   
    	    $form = $this->createForm(new InstitutionProfileFormType(), $this->institution, 
    	                    array(  InstitutionProfileFormType::OPTION_HIDDEN_FIELDS => array('') ,
                                    InstitutionProfileFormType::OPTION_BUBBLE_ALL_ERRORS => false, 
                                    InstitutionProfileFormType::OPTION_REMOVED_FIELDS => $removedFields));
    	    $formRequestData = $request->get($form->getName());
    	      if (isset($formRequestData['medicalProviderGroups']) ) {
                    // we always expect 1 medical provider group
                    // if it is empty remove it from the array
                    if (isset($formRequestData['medicalProviderGroups'][0]) && '' == trim($formRequestData['medicalProviderGroups'][0]) ) {
                        unset($formRequestData['medicalProviderGroups'][0]);
                    }
                     else {
                        $formRequestData['medicalProviderGroups'][0] = str_replace (array("\\'", '\\"'), array("'", '"'), $formRequestData['medicalProviderGroups'][0]);
                    }
                } 
    		$form->bind($formRequestData);

    		if ($form->isValid()) {
    			
    			$this->institution = $form->getData();   
    			$this->get('services.contact_detail')->removeInvalidContactDetails($this->institution);
    			$institution = $this->get('services.institution.factory')->save($this->institution);
    			$this->get('session')->setFlash('notice', "Successfully updated account");
    			 
    			//create event on editInstitution and dispatch
    			$this->get('event_dispatcher')->dispatch(InstitutionBundleEvents::ON_EDIT_INSTITUTION, $this->get('events.factory')->create(InstitutionBundleEvents::ON_EDIT_INSTITUTION, $institution));

    			// Invalidate Institution Profile cache
    			$this->get('services.memcache')->delete(FrontendMemcacheKeysHelper::generateInsitutionProfileKey($this->institution->getId()));    			

    		} else {
        	    $error = true;
                $form_errors = $this->get('validator')->validate($form);
                if($form_errors){
                    foreach ($form_errors as $_err) {
                        $errorArr[] = $_err->getMessage();
                    }
                }
    		}
    	}
    	return $this->render('AdminBundle:Institution:editDetails.html.twig', array(
			'form' => $form->createView(),
			'institution' => $this->institution,
            'medicalProvidersJSON' => \json_encode($medicalProviderGroupArr),
            'error' => $error,
            'error_list' => $errorArr,
    	));
    }
    
    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_INSTITUTION')")
     */
    public function viewAction(Request $request)
    {   
        $institutionService = $this->get('services.institution');
        $recentMedicalCenters = $this->get('services.institution')->getRecentlyAddedMedicalCenters($this->institution, new QueryOptionBag(array(QueryOption::LIMIT => 1)));
        $form = $this->createForm(new InstitutionProfileFormType(), $this->institution);
        return $this->render('AdminBundle:Institution:view.html.twig', array(
            'recentMedicalCenters' => $recentMedicalCenters,
            'institution' => $this->institution,
            'isSingleCenter' => $institutionService->isSingleCenter($this->institution),
            'institutionStatusForm' => $form->createView()
        ));
    }

   	public function addMediaAction(Request $request)
   	{
   	    $formParams = array('institutionId' => $this->institution->getId());

        $uploadFormAction = $this->generateUrl('admin_institution_media_upload', $formParams);

   	    return $this->render('AdminBundle:Institution:addMedia.html.twig', array(
            'institution' => $this->institution,
            'uploadFormAction' => $uploadFormAction,
            'multiUpload' => $request->get('multiUpload')
   	    ));
   	}

   	public function galleryAction(Request $request)
   	{
   	    $photos = $this->get('services.institution.gallery')->getInstitutionPhotos($this->institution->getId());

   	    $adapter = new ArrayAdapter($photos);

   	    $pager = new Pager($adapter, array('page' => $request->get('page'), 'limit' => 5));

   	    return $this->render('AdminBundle:Institution:gallery.html.twig', array(
            'institution' => $this->institution,
            'institutionMedia' => $pager
   	    ));
   	}
   	
   	/**
   	 * Upload Institution Logo
    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_INSTITUTION')")
     */
    public function updatePayingClientAction()
    {
        $request = $this->getRequest();
        $this->institution->setPayingClient((int)$request->get('payingClient'));

        $em = $this->getDoctrine()->getEntityManager();
        $em->persist($this->institution);
        $em->flush($this->institution);

        // dispatch EDIT institution event
        $event = $this->get('events.factory')->create(InstitutionBundleEvents::ON_EDIT_INSTITUTION, $this->institution);
        $this->get('event_dispatcher')->dispatch(InstitutionBundleEvents::ON_EDIT_INSTITUTION, $event);

        // Invalidate Institution Profile cache
        $this->get('services.memcache')->delete(FrontendMemcacheKeysHelper::generateInsitutionProfileKey($this->institution->getId()));

        return new Response(\json_encode(true),200, array('content-type' => 'application/json'));
    }
    
    public function editStatusAction(Request $request)
    {
        $template = 'AdminBundle:Institution/Modals:edit.institutionStatus.html.twig';
        $output = array();
        if ($request->isMethod('POST')) {
            $formVariables = $request->get(InstitutionProfileFormType::NAME);
            $removedFields = \array_diff(InstitutionProfileFormType::getFieldNames(), array_keys($formVariables));
            $form = $this->createForm(new InstitutionProfileFormType(), $this->institution, array(InstitutionProfileFormType::OPTION_BUBBLE_ALL_ERRORS => false, InstitutionProfileFormType::OPTION_REMOVED_FIELDS => $removedFields));
            $formRequestData = $request->get($form->getName());
            $form->bind($formRequestData);
    
            if ($form->isValid()) {
                $this->institution = $form->getData();
                $this->get('services.institution.factory')->save($this->institution);

                // dispatch UPDATE_STATUS institution event
                $this->get('event_dispatcher')->dispatch(InstitutionBundleEvents::ON_UPDATE_STATUS_INSTITUTION, new GenericEvent($this->institution));

                $response = new Response(\json_encode(array('html' => '<strong>Success!</strong> Updated status for '.$this->institution->getName().'.', 'status' => $this->institution->getStatus())),200, array('content-type' => 'application/json'));
                
                // Invalidate Institution Profile cache
                $this->get('services.memcache')->delete(FrontendMemcacheKeysHelper::generateInsitutionProfileKey($this->institution->getId()));

                // Invalidate All InstitutionMedicalCenterProfile cache
                $centers = $this->institution->getInstitutionMedicalCenters();
                foreach($centers as $each) {
                    $this->get('services.memcache')->delete(FrontendMemcacheKeysHelper::generateInsitutionMedicalCenterProfileKey($each->getId()));                    
                }
            }
            else {
                $response = new Response(\json_encode(array('error' => 'Please fill up the form propery')),400, array('content-type' => 'application/json'));
            }
        }
        else {
            
            $form = $this->createForm(new InstitutionProfileFormType(), $this->institution);
            $output['html'] =  $this->renderView($template, array(
                            'institution' => $this->institution,
                            'institutionStatusForm' => $form->createView()
            ));
            $response = new Response(\json_encode($output),200, array('content-type' => 'application/json'));
        }
        
        return $response;
    }
    
   	/**
   	 * Upload logo for Institution
   	 * @param Request $request
   	 */
   	public function uploadLogoAction(Request $request)
   	{
   	    if (($fileBag = $request->files) && $fileBag->has('file')) {
   	        $media = $this->get('services.institution.media')->uploadLogo($fileBag->get('file'), $this->institution);
   	        if(!$media) {
   	            $this->get('session')->setFlash('error', 'Unable to Upload Image');
   	        }

   	        // Invalidate Institution Profile cache
   	        $this->get('services.memcache')->delete(FrontendMemcacheKeysHelper::generateInsitutionProfileKey($this->institution->getId()));
   	    }

   	    return $this->redirect($request->headers->get('referer'));
   	}
   	
   	/**
   	 * Upload Institution FeaturedImage
   	 * @param Request $request
   	 */
   	public function uploadFeaturedImageAction(Request $request)
   	{

   	    if (($fileBag = $request->files) && $fileBag->has('file')) {
   	        $media = $this->get('services.institution.media')->uploadFeaturedImage($fileBag->get('file'), $this->institution);
   	        if(!$media) {
   	            $this->get('session')->setFlash('error', 'Unable to Upload Featured Image');
   	        }

   	        // Invalidate Institution Profile cache
   	        $this->get('services.memcache')->delete(FrontendMemcacheKeysHelper::generateInsitutionProfileKey($this->institution->getId()));
   	    }
   	
   	    return $this->redirect($request->headers->get('referer'));
   	}
   	
   	/**
   	 * Upload Institution Media for Gallery
   	 * @param Request $request
   	 */
   	public function uploadMediaAction(Request $request)
   	{
   	    $response = new Response(json_encode(true));
   	    $response->headers->set('Content-Type', 'application/json');
   	
   	    if (($fileBag = $request->files) && $fileBag->has('file')) {
   	        $media = $this->get('services.institution.media')->uploadToGallery($fileBag->get('file'), $this->institution);
   	        if(!$media) {
   	            $response = new Response('Error', 500);
   	        }

   	        // Invalidate Institution Profile cache
   	        $this->get('services.memcache')->delete(FrontendMemcacheKeysHelper::generateInsitutionProfileKey($this->institution->getId()));
   	    }
   	
   	    return $response;
   	}
   	
   	/**
   	 * @author Chaztine Blance
   	 * @param Request $request
   	 * @return \Symfony\Component\HttpFoundation\Response
   	 */
   	public function viewInstitutionDoctorsAction(Request $request)
   	{
   	    $pagerAdapter = new DoctrineOrmAdapter($this->getDoctrine()->getRepository('DoctorBundle:Doctor')->getAllDoctorsByInstitution($this->institution));
   	    $pagerParams = array(
   	                    'page' => $request->get('page', 1),
   	                    'limit' => 20
   	    );
   	    $pager = new Pager($pagerAdapter, $pagerParams);
   	    
   	    return $this->render('AdminBundle:Institution:viewInstitutionDoctors.html.twig', array(
                'institutionDoctors' => $pager->getResults(),
                'pager' => $pager,
                'institution' => $this->institution,
   	    ));
   	}
}