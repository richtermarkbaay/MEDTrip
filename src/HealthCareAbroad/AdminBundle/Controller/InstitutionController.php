<?php

namespace HealthCareAbroad\AdminBundle\Controller;

use HealthCareAbroad\InstitutionBundle\Form\InstitutionUserSignUpFormType;

use HealthCareAbroad\HelperBundle\Entity\ContactDetailTypes;

use HealthCareAbroad\HelperBundle\Entity\ContactDetail;

use HealthCareAbroad\InstitutionBundle\Services\InstitutionMediaService;

use HealthCareAbroad\PagerBundle\Pager;

use HealthCareAbroad\InstitutionBundle\Entity\MedicalProviderGroup;

use HealthCareAbroad\HelperBundle\Classes\QueryOption;

use HealthCareAbroad\HelperBundle\Classes\QueryOptionBag;

use HealthCareAbroad\InstitutionBundle\Form\InstitutionProfileFormType;

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
    public function indexAction()
    {
        $params = array(
            'pager' => $this->pager,
            'institutions' => $this->filteredResult, 
            'statusList' => InstitutionStatus::getBitValueLabels(),
            'updateStatusOptions' => InstitutionStatus::getBitValueForActiveStatus()
        );

        return $this->render('AdminBundle:Institution:index.html.twig', $params);
    }
    
    /**
     * Add new User and Institution
     * @author Chaztine Blance
     */
    public function addAction(Request $request){
        $medicalProviderGroup = $this->getDoctrine()->getRepository('InstitutionBundle:MedicalProviderGroup')->getActiveMedicalGroups();
    	$factory = $this->get('services.institution.factory');
    	$institution = $factory->createInstance();  	
    	$institutionUser = new InstitutionUser();
    	$phoneNumber = new ContactDetail();
    	$phoneNumber->setType(ContactDetailTypes::PHONE);
    	$institutionUser->addContactDetail($phoneNumber);
    	
    	$mobileNumber = new ContactDetail();
    	$mobileNumber->setType(ContactDetailTypes::MOBILE);
    	$institutionUser->addContactDetail($mobileNumber);
    	$form = $this->createForm(new InstitutionUserSignUpFormType(), $institutionUser);
    
	    	if ($request->isMethod('POST')) {
	    		$form->bind($request);
	    		 
	    		if ($form->isValid()) {

	    		    $institution = $form->getData();
	    			// initialize required database fields
	    			$institution->setAddress1('');
	    			$institution->setContactEmail('');
	    			$institution->setContactNumber('');
	    			$institution->setDescription('');
	    			$institution->setLogo(null);
	    			$institution->setCoordinates('');
	    			$institution->setState('');
	    			$institution->setWebsites('');
	    			$institution->setStatus(InstitutionStatus::getBitValueForInactiveStatus());
	    			$institution->setZipCode('');
	    			$institution->setSignupStepStatus(0);

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
    public function addDetailsAction(Request $request)
    {

        if(!$this->institution->getContactDetails()->count()) {
            $contactDetails = new ContactDetail();
            $contactDetails->setType(ContactDetailTypes::PHONE);
            $this->institution->addContactDetail($contactDetails);
        }
	    $form = $this->createForm(new InstitutionProfileFormType(), $this->institution, array(InstitutionProfileFormType::OPTION_HIDDEN_FIELDS => array('name')));

	    // redirect to edit institution if status is already active
	    
	    if($this->get('services.institution')->isActive($this->institution)){
	        
	        return $this->redirect($this->generateUrl('admin_institution_edit_details', array('institutionId' => $this->institution->getId())));
	    }
	    
	    if ($request->isMethod('POST')) {
	        
	        $form->bindRequest($request);
	        
	        if ($form->isValid()) {
	            $institution = $form->getData();
	            
	            // update the sign up step status
	            $institution->setSignupStepStatus(0);
	            
	            // update to active status
	            $institution->setStatus(InstitutionStatus::getBitValueForActiveStatus());
	            $this->get('services.institution.factory')->save($institution);
	    		$this->get('session')->setFlash('notice', "Successfully completed details of {$institution->getName()}.");
	    
	    		return $this->redirect($this->generateUrl('admin_institution_view', array('institutionId' => $this->institution->getId())));
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
    public function editDetailsAction(Request $request)
    {

        if(!$this->institution->getContactDetails()->count()) {
            $contactDetails = new ContactDetail();
            $contactDetails->setType(ContactDetailTypes::PHONE);
            $this->institution->addContactDetail($contactDetails);
        }
        
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

   	public function addMediaAction(Request $request)
   	{
   	    $formParams = array('institutionId' => $this->institution->getId());

   	    if($request->get('imcId')) {
   	        $formParams['imcId'] = $request->get('imcId');
   	        $uploadFormAction = $this->generateUrl('admin_institution_medicalCenter_media_upload', $formParams);
   	    } else {
   	        $uploadFormAction = $this->generateUrl('admin_institution_media_upload', $formParams);
   	    }

   	    return $this->render('AdminBundle:Institution:addMedia.html.twig', array(
            'institution' => $this->institution,
            'uploadFormAction' => $uploadFormAction,
            'multiUpload' => $request->get('multiUpload')
   	    ));
   	}

   	public function galleryAction(Request $request)
   	{
   	    $adapter = new ArrayAdapter($this->get('services.institution.media')->getMediaGallery($this->institution));
   	    $pager = new Pager($adapter, array('page' => $request->get('page'), 'limit' => 5));
   	
   	    return $this->render('AdminBundle:Institution:gallery.html.twig', array(
            'institution' => $this->institution,
            'institutionMedia' => $pager
   	    ));
   	}
   	
   	/**
   	 * Upload Institution Logo
   	 * @param Request $request
   	 */
   	public function uploadLogoAction(Request $request)
   	{
   	    if (($fileBag = $request->files) && $fileBag->has('file')) {
   	        $media = $this->get('services.institution.media')->uploadLogo($fileBag->get('file'), $this->institution);
   	        if(!$media) {
   	            $this->get('session')->setFlash('error', 'Unable to Upload Image');
   	        }
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
   	        //var_dump($fileBag->get('file')); exit;
   	        $media = $this->get('services.institution.media')->uploadFeaturedImage($fileBag->get('file'), $this->institution);
   	        if(!$media) {
   	            $this->get('session')->setFlash('error', 'Unable to Upload Featured Image');
   	        }
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
   	    }
   	
   	    return $response;
   	}
}