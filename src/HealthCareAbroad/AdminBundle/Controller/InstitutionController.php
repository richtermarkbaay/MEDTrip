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

        // Check institutionMedicalCenter        
        if ($request->get('imcId')) {
            $this->institutionMedicalCenter = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionMedicalCenter')->find($request->get('imcId'));
            if(!$this->institutionMedicalCenter) {
                throw $this->createNotFoundException('Invalid institutionMedicalCenter.');
            }
        }

        // Check InstitutionSpecialization
        if ($request->get('isId')) {
            $this->institutionSpecialization = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionSpecialization')->find($request->get('isId'));

            if(!$this->institutionSpecialization) {
                throw $this->createNotFoundException('Invalid InstitutionSpecialization.');
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
    
    public function addAction(Request $request){
    
    	$institutionType = $request->get('institutionType', InstitutionTypes::MEDICAL_GROUP_NETWORK_MEMBER);
    
    	$factory = $this->get('services.institution.factory');
    	$institution = $factory->createByType($institutionType);
    	
    	//$form = $this->createForm(new InstitutionCreateFormType(), $institution);
    	
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
    
    public function addDetailsAction(Request $request){
    	
    	$id = $request->get('id', null);

    	$institution = $this->getDoctrine()->getRepository('InstitutionBundle:Institution')->find($id);
	    $form = $this->createForm(new InstitutionFormType(), $institution);
	    	
	    return $this->render('AdminBundle:Institution:addDetails.html.twig', array(
	    		    							'form' => $form->createView(),
	    		    							'institution' => $institution
	    ));
    }
    
    
    /**
     * Save Institution Details
     */
    public function saveAction(){
    	$request = $this->getRequest();
    	//update institution details
    	if ($request->isMethod('POST')) {
    		// Get contactNumbers and convert to json format
    		$contactNumber = json_encode($request->get('contactNumber'));
    		$websites = json_encode($request->get('website'));
    		 
    		$form->bindRequest($request);
    			
    		if ($form->isValid()) {
    	
    			// Set Contact Number before saving
    			$form->getData()->setContactNumber($contactNumber);
    			$form->getData()->setWebsites($websites);
    	
    			$institution = $this->get('services.institution.factory')->save($form->getData());
    			$this->get('session')->setFlash('notice', "Successfully updated account");
    			 
    			//create event on editInstitution and dispatch
    			$this->get('event_dispatcher')->dispatch(InstitutionBundleEvents::ON_EDIT_INSTITUTION, $this->get('events.factory')->create(InstitutionBundleEvents::ON_EDIT_INSTITUTION, $institution));
    			 
    		}
    	}
    	
    	return $this->render('AdminBundle:Institution:index.html.twig');
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
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_INSTITUTION')")
     */
    public function manageCentersAction()
    {
        $params = array(
            'institutionId' => $this->institution->getId(),
            'institutionName' => $this->institution->getName(),
            'centerStatusList' => InstitutionMedicalCenterStatus::getStatusList(),
            'updateCenterStatusOptions' => InstitutionMedicalCenterStatus::getUpdateStatusOptions(), 
            'institutionMedicalCenters' => $this->filteredResult,
            'pager' => $this->pager
        );

        return $this->render('AdminBundle:Institution:manage_centers.html.twig', $params);
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

    /**
     * This is the first step when creating a new institutionMedicalCenter. Add details of a institutionMedicalCenter
     * 
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addMedicalCenterDetailsAction(Request $request)
    {
        if (is_null($this->institutionMedicalCenter)) {
            $this->institutionMedicalCenter = new institutionMedicalCenter();
            $this->institutionMedicalCenter->setInstitution($this->institution);
        }
        else {
            // there is an imcgId in the Request, check if this is a draft
            if ($this->institutionMedicalCenter && !$this->service->isDraft($this->institutionMedicalCenter)) {
                return $this->_redirectIndexWithFlashMessage('Invalid draft medical center', 'error');
            }
        }

        $form = $this->createForm(new InstitutionMedicalCenterFormType(),$this->institutionMedicalCenter);
        if ($request->isMethod('POST')) {
            $form->bind($request);
            
            if ($form->isValid()) {
                
                $this->institutionMedicalCenter = $this->get('services.institutionMedicalCenter')
                    ->saveAsDraft($form->getData());
                
                // TODO: fire event
                
                // redirect to step 2;
                return $this->redirect($this->generateUrl('institution_medicalCenter_addSpecializations',array('imcgId' => $this->institutionMedicalCenter->getId())));
            }
        }
        
        $params = array(
            'form' => $form->createView(),
            'institutionId' => $this->institution->getId(),
            'institutionMedicalCenter' => $this->institutionMedicalCenter
        );
        
        return $this->render('AdminBundle:Institution:form.medicalCenter.html.twig', $params);
    }

//     /**
//      * This is the second step when creating a new institutionMedicalCenter. Add specializations for specific medicalCenter 
//      *
//      * @param Request $request
//      * @return \Symfony\Component\HttpFoundation\Response
//      */
//     public function addSpecializationAction(Request $request)
//     {
//         if (is_null($this->institutionSpecialization)) {
//             $this->institutionSpecialization = new InstitutionSpecialization();
//         }

//         $form = $this->createForm(new InstitutionMedicalCenterFormType(),$this->institutionSpecialization);
//         if ($request->isMethod('POST')) {
//             $form->bind($request);
    
//             if ($form->isValid()) {
    
//                 // TODO: Save specialization
    
//                 // TODO: fire event

//                 // redirect to step 2;
//                 //return $this->redirect($this->generateUrl());
//             }
//         }
    
//         $params = array(
//             'form' => $form->createView(),
//             'institutionId' => $this->institution->getId(),
//             'institutionSpecialization' => $this->institutionSpecialization
//         );
    
//         return $this->render('AdminBundle:Institution:form.medicalCenter.html.twig', $params);
//     }
    
//     /**
//      *
//      * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_INSTITUTION')")
//      */
//     public function editSpecializationAction()
//     {
//         $form = $this->createForm(new institutionMedicalCenterType(), $this->institutionMedicalCenter);

//         $formActionParams = array('institutionId' => $this->institution->getId(), 'imcId' => $this->institutionMedicalCenter->getId());
//         $formAction = $this->generateUrl('admin_institution_medicalCenter_update', $formActionParams);

//         $params = array(
//             'institutionId' => $this->institution->getId(),
//             'institutionMedicalCenter' => $this->institutionMedicalCenter,
//             'centerStatusList' => InstitutionMedicalCenterStatus::getStatusList(),
//             'updateCenterStatusOptions' => InstitutionMedicalCenterStatus::getUpdateStatusOptions(),
//             'formAction' => $formAction,
//             'form' => $form->createView()
//         );

//         return $this->render('AdminBundle:Institution:form.medicalCenter.html.twig', $params);
//     }
    
//     /**
//      *
//      * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_INSTITUTION')")
//      */
//     public function saveSpecializationAction()
//     {
//         $request = $this->getRequest();

//         if(!$request->isMethod('POST')) {
//             return new Response("Save requires POST method!", 405);
//         }

//         if(!$this->institutionMedicalCenter) {
//             $this->institutionMedicalCenter = new institutionMedicalCenter;
//             $this->institutionMedicalCenter->setInstitution($this->institution);
//         }

//         $form = $this->createForm(new institutionMedicalCenterType(), $this->institutionMedicalCenter);
//         $form->bind($request);

//         if($form->isValid()) {
//             if(!$request->get('imcId'))
//                 $this->institutionMedicalCenter->setStatus(InstitutionMedicalCenterStatus::INACTIVE);

//             $em = $this->getDoctrine()->getEntityManager();
//             $em->persist($this->institutionMedicalCenter);
//             $em->flush($this->institutionMedicalCenter);

//             // dispatch ADD or EDIT institutionMedicalCenter event
//             $actionEvent = $request->get('imcId') ? InstitutionBundleEvents::ON_EDIT_INSTITUTION_MEDICAL_CENTER : InstitutionBundleEvents::ON_ADD_INSTITUTION_MEDICAL_CENTER;
//             $event = $this->get('events.factory')->create($actionEvent, $this->institutionMedicalCenter, array('institutionId' => $this->institution->getId()));
//             $this->get('event_dispatcher')->dispatch($actionEvent, $event);

//             $request->getSession()->setFlash('success', 'Medical center has been saved!');

//             if($request->get('submit') == 'Save') {
//                 $routeParams = array('institutionId' => $this->institution->getId(), 'imcId' => $this->institutionMedicalCenter->getId());

//                 return $this->redirect($this->generateUrl('admin_institution_medicalCenter_edit', $routeParams));
//             } else {            
//                 return $this->redirect($this->generateUrl('admin_institution_medicalCenter_add', array('institutionId' => $this->institution->getId())));
//             }

//         } else {

//             if($this->institutionMedicalCenter->getId()) {
//                 $formActionParams = array('institutionId' => $this->institution->getId(), 'imcId' => $this->institutionMedicalCenter->getId());
//                 $formAction = $this->generateUrl('admin_institution_medicalCenter_update', $formActionParams);
//             } else {
//                 $formActionParams = array('institutionId' => $this->institution->getId());
//                 $formAction = $this->generateUrl('admin_institution_medicalCenter_create', $formActionParams);
//             }

//             $params = array(
//                 'form' => $form->createView(),
//                 'institutionId' => $this->institution->getId(),
//                 'institutionMedicalCenter' => $this->institutionMedicalCenter,
//                 'formAction' => $formAction
//             );

//             return $this->render('AdminBundle:Institution:form.medicalCenter.html.twig', $params);
//         }
//     }


//     /**
//      * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_INSTITUTION')")
//      */
//     public function addProcedureTypeAction()
//     {    
//         $institutionSubSpecialization = new InstitutionSubSpecialization();
//         $institutionSubSpecialization->setinstitutionMedicalCenter($this->institutionMedicalCenter);

//         $form = $this->createForm(new InstitutionSubSpecializationFormType(), $institutionSubSpecialization);

//         return $this->render("AdminBundle:Institution:modalForm.treatment.html.twig", array(
//             'institution' => $this->institution,
//             'institutionMedicalCenter' => $this->institutionMedicalCenter,
//             'institutionSubSpecialization' => $this->institutionSubSpecialization,
//             'form' => $form->createView(),
//             'newProcedureType' => true
//         ));
//     }

//     /**
//      * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_INSTITUTION')")
//      */
//     public function editProcedureTypeAction()
//     {
//         $form = $this->createForm(new InstitutionSubSpecializationFormType(), $this->institutionSubSpecialization);

//         return $this->render("AdminBundle:Institution:modalForm.treatment.html.twig", array(
//             'institution' => $this->institution,
//             'institutionMedicalCenter' => $this->institutionMedicalCenter,
//             'institutionSubSpecialization' => $this->institutionSubSpecialization,
//             'form' => $form->createView(),
//             'newProcedureType' => false
//         ));
//     }

//     /**
//      * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_INSTITUTION')")
//      */    
//     public function saveProcedureTypeAction()
//     {
//         $request = $this->getRequest();

//         if (!$request->isMethod('POST')) {
//             return new Response('Unsupported method', 405);
//         }

//         if(!$this->institutionSubSpecialization) {
//             $this->institutionSubSpecialization = new InstitutionSubSpecialization();
//             $this->institutionSubSpecialization->setinstitutionMedicalCenter($this->institutionMedicalCenter);            
//         }

//         $form = $this->createForm(new InstitutionSubSpecializationFormType(), $this->institutionSubSpecialization);
//         $form->bindRequest($request);

//         if ($form->isValid()){
//             $this->institutionSubSpecialization->setStatus(InstitutionSubSpecialization::STATUS_ACTIVE);

//             $em = $this->getDoctrine()->getEntityManager();
//             $em->persist($this->institutionSubSpecialization);
//             $em->flush($this->institutionSubSpecialization);

//             // dispatch EDIT institutionSubSpecialization event
//             $actionEvent = $request->get('issId') 
//                 ? InstitutionBundleEvents::ON_EDIT_INSTITUTION_TREATMENT 
//                 : InstitutionBundleEvents::ON_ADD_INSTITUTION_TREATMENT;
//             $event = $this->get('events.factory')->create($actionEvent, $this->institutionSubSpecialization);
//             $this->get('event_dispatcher')->dispatch($actionEvent, $event);
            
//             $request->getSession()->setFlash('success', 'Successfully saved institution treatement.');

//             $url = $this->generateUrl('admin_institution_medicalCenter_edit', array('institutionId' => $this->institution->getId(), 'imcId' => $this->institutionMedicalCenter->getId(), 'issId' => $this->institutionSubSpecialization->getId()));

//             $response = new Response(json_encode(array('redirect_url' => $url)));
//             $response->headers->set('Content-Type', 'application/json');
            
//             return $response;
//         }

//         return $this->render('AdminBundle:Institution:modalForm.treatment.html.twig', array(
//             'institution' => $this->institution,
//             'institutionMedicalCenter' => $this->institutionMedicalCenter,
//             'institutionSubSpecialization' => $this->institutionSubSpecialization,
//             'form' => $form->createView(),
//             'newProcedureType' => !$request->get('issId'),
//         ));
//     }

//     /**
//      * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_INSTITUTION')")
//      */
//     public function addProcedureAction()
//     {
//         $request = $this->getRequest();
//         $em = $this->getDoctrine()->getEntityManager();

//         $institutionTreatment = new InstitutionSubSpecializationProcedure();
//         $institutionTreatment->setInstitutionSubSpecialization($this->institutionSubSpecialization);
//         $form = $this->createForm(new InstitutionSubSpecializationProcedureFormType(), $institutionTreatment);

//         $formActionParams = array(
//             'institutionId' => $this->institution->getId(),
//             'imcId' => $this->institutionMedicalCenter->getId(),
//             'issId' => $this->institutionSubSpecialization->getId(),
//         );

//         $formAction = $this->generateUrl('admin_institution_treatmentProcedure_create', $formActionParams);
        
//         $params = array(
//             'institutionId' => $this->institution->getId(),
//             'institutionMedicalCenter' => $this->institutionMedicalCenter,
//             'institutionSubSpecialization' => $this->institutionSubSpecialization,
//             'formAction' => $formAction,
//             'isNew' => true,
//             'form' => $form->createView()
//         );

//         return $this->render('AdminBundle:Institution:modalForm.procedure.html.twig', $params);
//     }

//     /**
//      * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_INSTITUTION')")
//      */
//     public function editProcedureAction()
//     {
//         $form = $this->createForm(new InstitutionSubSpecializationProcedureFormType(), $this->institutionTreatment);
//         $formActionParams = array(
//             'institutionId' => $this->institution->getId(),
//             'imcId' => $this->institutionMedicalCenter->getId(),
//             'issId' => $this->institutionSubSpecialization->getId(),
//             'itId' => $this->institutionTreatment->getId()
//         );

//         $formAction = $this->generateUrl('admin_institution_treatmentProcedure_update', $formActionParams);

//         $params = array(
//             'institutionId' => $this->institution->getId(),
//             'institutionMedicalCenter' => $this->institutionMedicalCenter,
//             'institutionSubSpecialization' => $this->institutionSubSpecialization,
//             'treatmentProcedureName' => $this->institutionTreatment->getSubSpecializationProcedure()->getName(),
//             'formAction' => $formAction,
//             'isNew' => false,
//             'form' => $form->createView()
//         );

//         return $this->render('AdminBundle:Institution:modalForm.procedure.html.twig', $params);
//     }

//     /**
//      * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_INSTITUTION')")
//      * @return \Symfony\Component\HttpFoundation\RedirectResponse
//      */
//     public function saveProcedureAction()
//     {
//         $request = $this->getRequest();

//         if (!$request->isMethod('POST')) {
//             return new Response('Unsupported method', 405);
//         }

//         if (!$this->institutionTreatment) {
//             $this->institutionTreatment = new InstitutionSubSpecializationProcedure();
//             $this->institutionTreatment->setInstitutionSubSpecialization($this->institutionSubSpecialization);
//         }

//         $form = $this->createForm(new InstitutionSubSpecializationProcedureFormType(), $this->institutionTreatment);
//         $form->bindRequest($request);

//         if ($form->isValid()) {
//             $em = $this->getDoctrine()->getEntityManager();
//             $em->persist($this->institutionTreatment);
//             $em->flush($this->institutionTreatment);
            
//             // dispatch ADD or EDIT institutionTreatment event
//             $actionEvent = $request->get('itId') 
//                 ? InstitutionBundleEvents::ON_EDIT_INSTITUTION_TREATMENT_PROCEDURE 
//                 : InstitutionBundleEvents::ON_ADD_INSTITUTION_TREATMENT_PROCEDURE;
//             $event = $this->get('events.factory')->create($actionEvent, $this->institutionTreatment);
//             $this->get('event_dispatcher')->dispatch($actionEvent, $event);
            
//             $request->getSession()->setFlash('success', "Successfully added a medical procedure to \"{$this->institutionSubSpecialization->getSubSpecialization()->getName()}\" treatment.");

//             $params = array(
//                  'institutionId' => $this->institution->getId(),
//                  'imcId' => $this->institutionMedicalCenter->getId(),
//                  'issId' => $this->institutionSubSpecialization->getId()
//             );

//             $url = $this->generateUrl('admin_institution_medicalCenter_edit', $params);

//             $response = new Response(json_encode(array('redirect_url' => $url)));
//             $response->headers->set('Content-Type', 'application/json');

//             return $response;

//         } else {

//             $formActionParams = array(
//                 'institutionId' => $this->institution->getId(),
//                 'imcId' => $this->institutionMedicalCenter->getId(),
//                 'issId' => $this->institutionSubSpecialization->getId(),
//             );

//             if(!$request->get('itId')) {
//                 $formAction = $this->generateUrl('admin_institution_treatmentProcedure_create', $formActionParams);
//                 $params['isNew'] = true;

//             } else {
//                 $formActionParams['itId'] = $this->institutionTreatment->getId();
//                 $formAction = $this->generateUrl('admin_institution_treatmentProcedure_update', $formActionParams);

//                 $params['isNew'] = true;
//                 $params['treatmentProcedureName'] = $this->institutionTreatment->getSubSpecializationProcedure()->getName();
//             }

//             $params['institutionId'] = $this->institution->getId();
//             $params['institutionMedicalCenter'] = $this->institutionMedicalCenter;
//             $params['institutionSubSpecialization'] = $this->institutionSubSpecialization;
//             $params['formAction'] = $formAction;
//             $params['form'] = $form->createView();
            
//             return $this->render('AdminBundle:Institution:modalForm.procedure.html.twig', $params);
//         }
//     }

//     /**
//      * 
//      * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_INSTITUTION')")
//      */
//     public function updateProcedureStatusAction()
//     {
//         $status = $this->institutionTreatment->getStatus() == InstitutionSubSpecializationProcedure::STATUS_ACTIVE
//             ? InstitutionSubSpecializationProcedure::STATUS_INACTIVE
//             : InstitutionSubSpecializationProcedure::STATUS_ACTIVE;

//         $this->institutionTreatment->setStatus($status);
        
//         $em = $this->getDoctrine()->getEntityManager();
//         $em->persist($this->institutionTreatment);
//         $em->flush($this->institutionTreatment);
        
//         // dispatch ADD or EDIT institutionTreatment event
//         $actionEvent = InstitutionBundleEvents::ON_EDIT_INSTITUTION_TREATMENT_PROCEDURE;
//         $event = $this->get('events.factory')->create($actionEvent, $this->institutionTreatment);
//         $this->get('event_dispatcher')->dispatch($actionEvent, $event);

//         $response = new Response(json_encode(true));
//         $response->headers->set('Content-Type', 'application/json');

//         return $response;
//     }
}