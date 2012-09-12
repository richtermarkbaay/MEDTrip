<?php

namespace HealthCareAbroad\AdminBundle\Controller;


use HealthCareAbroad\InstitutionBundle\Entity\InstitutionStatus;

use HealthCareAbroad\MedicalProcedureBundle\Entity\MedicalCenter;
use HealthCareAbroad\MedicalProcedureBundle\Entity\MedicalProcedure;

use HealthCareAbroad\InstitutionBundle\Entity\Institution;
use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter;
use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalProcedure;
use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalProcedureType;
use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenterStatus;

use HealthCareAbroad\InstitutionBundle\Form\InstitutionMedicalCenterType;
use HealthCareAbroad\InstitutionBundle\Form\InstitutionMedicalProcedureFormType;
use HealthCareAbroad\InstitutionBundle\Form\InstitutionMedicalProcedureTypeFormType;

use HealthCareAbroad\InstitutionBundle\Event\InstitutionEvents;
use HealthCareAbroad\InstitutionBundle\Event\InstitutionMedicalCenterEvents;
use HealthCareAbroad\InstitutionBundle\Event\InstitutionMedicalProcedureEvents;
use HealthCareAbroad\InstitutionBundle\Event\InstitutionMedicalProcedureTypeEvents;

use HealthCareAbroad\InstitutionBundle\Event\CreateInstitutionEvent;
use HealthCareAbroad\InstitutionBundle\Event\CreateInstitutionMedicalCenterEvent;
use HealthCareAbroad\InstitutionBundle\Event\CreateInstitutionMedicalProcedureEvent;
use HealthCareAbroad\InstitutionBundle\Event\CreateInstitutionMedicalProcedureTypeEvent;

use HealthCareAbroad\AdminBundle\Events\MedicalCenterEvents;
use HealthCareAbroad\AdminBundle\Events\CreateMedicalCenterEvent;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use JMS\SecurityExtraBundle\Annotation\PreAuthorize;

class InstitutionController extends Controller
{	
    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_VIEW_INSTITUTIONS')")
     */
	public function indexAction()
	{
		$params = array(
		    'institutions' => $this->filteredResult, 
            'statusList' => InstitutionStatus::getStatusList(),
		    'updateStatusOptions' => InstitutionStatus::getUpdateStatusOptions()
		);

		return $this->render('AdminBundle:Institution:index.html.twig', $params);
	}
	
	/**
	 * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_INSTITUTION')")
	 */
	public function viewAction(Request $request)
	{
	    $institution = $this->getDoctrine()->getRepository('InstitutionBundle:Institution')->find($request->get('institutionId', 0));
	    if (!$institution) {
	        throw $this->createNotFoundException('Invalid institution');
	    }
	    
	    return $this->render('AdminBundle:Institution:view.html.twig', array('institution' => $institution));
	}
	
	/**
	 * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_DELETE_INSTITUTION')")
	 * @param int $institutionId
	 */
	public function updateStatusAction($institutionId)
	{
		$request = $this->getRequest();
		$em = $this->getDoctrine()->getEntityManager();
		$institution = $em->getRepository('InstitutionBundle:Institution')->find($institutionId);

		if($institution) {

		    if(!InstitutionStatus::isValid($request->get('status'))) {
		        $request->getSession()->setFlash('error', 'Unable to update status. ' . $request->get('status') . ' is invalid status value!');

		        return $this->redirect($this->generateUrl('admin_institution_index'));
		    }

			$institution->setStatus($request->get('status'));
			$em->persist($institution);
			$em->flush($institution);

			//TODO:: to create listener for the dispatch event of editInstitution Event
			$event = new CreateInstitutionEvent($institution);
			$this->get('event_dispatcher')->dispatch(InstitutionEvents::ON_EDIT_INSTITUTION, $event);
		}

		$request->getSession()->setFlash('success', '"'.$institution->getName().'" has been updated!');

		return $this->redirect($this->generateUrl('admin_institution_index'));
	}

	/**
	 * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_INSTITUTION')")
	 * @param int $institutionId
	 */
	public function manageCentersAction($institutionId)
	{
		$em = $this->getDoctrine()->getEntityManager();
		$institution = $em->getRepository('InstitutionBundle:Institution')->find($institutionId);

		$params = array(
			'institutionId' => $institutionId,
			'institutionName' => $institution->getName(),
			'centerStatusList' => InstitutionMedicalCenterStatus::getStatusList(),
			'updateCenterStatusOptions' => InstitutionMedicalCenterStatus::getUpdateStatusOptions(), 
			'institutionMedicalCenters' => $this->filteredResult,
		);

		return $this->render('AdminBundle:Institution:manage_centers.html.twig', $params);
	}

	/**
	 *
	 * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_INSTITUTION')")
	 * @param int $institutionId
	 */
	public function addMedicalCenterAction($institutionId)
	{
	    $institutionId = $this->getRequest()->get('institutionId', 0);
		$institution = $this->getDoctrine()->getRepository('InstitutionBundle:Institution')->find($institutionId);
		
		if(!$institution) {
            throw $this->createNotFoundException('Invalid institution');
        }

		$institutionMedicalCenter = new InstitutionMedicalCenter();
        $institutionMedicalCenter->setInstitution($institution);

		$form = $this->createForm(new InstitutionMedicalCenterType(), $institutionMedicalCenter);

		$formAction = $this->generateUrl('admin_institution_medicalCenter_create', array('institutionId' => $institutionId));

		$params = array(
			'institutionId' => $institutionId,
			'institutionMedicalCenter' => $institutionMedicalCenter,
			'formAction' => $formAction,
			'form' => $form->createView()
		);

		return $this->render('AdminBundle:Institution:form.medicalCenter.html.twig', $params);	
	}

	/**
	 *
	 * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_INSTITUTION')")
	 * @param int $institutionId
	 */
	public function editMedicalCenterAction($institutionId)
	{
		$institutionMedicalCenterId = $this->getRequest()->get('imcId', 0);
		$institutionMedicalCenter = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionMedicalCenter')->find($institutionMedicalCenterId);

		if(!$institutionMedicalCenter) {
		    throw $this->createNotFoundException('Invalid InstitutionMedicalCenter.');
		}
		
		$form = $this->createForm(new InstitutionMedicalCenterType(), $institutionMedicalCenter);

		$formActionParams = array('institutionId' => $institutionId, 'imcId' => $institutionMedicalCenter->getId());
		$formAction = $this->generateUrl('admin_institution_medicalCenter_update', $formActionParams);
		
		$params = array(
			'institutionId' => $institutionId,
			'institutionMedicalCenter' => $institutionMedicalCenter,
			'formAction' => $formAction,
			'form' => $form->createView()
		);

		return $this->render('AdminBundle:Institution:form.medicalCenter.html.twig', $params);
	}
	
	/**
	 *
	 * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_INSTITUTION')")
	 * @param int $institutionId
	 */
	public function saveMedicalCenterAction($institutionId)
	{
		$request = $this->getRequest();

		if('POST' != $request->getMethod()) {
			return new Response("Save requires POST method!", 405);
		}

		$em = $this->getDoctrine()->getEntityManager();

		if($institutionMedicalCenterId = $request->get('imcId')) {
			$institutionMedicalCenter = $em->getRepository('InstitutionBundle:InstitutionMedicalCenter')->find($institutionMedicalCenterId);
		} else {
			$institution = $em->getRepository('InstitutionBundle:Institution')->find($institutionId);

			if(!$institution) {
			    throw $this->createNotFoundException('Invalid Institution.');
			}

			$institutionMedicalCenter = new InstitutionMedicalCenter;
			$institutionMedicalCenter->setInstitution($institution);
		}

		$form = $this->createForm(new InstitutionMedicalCenterType(), $institutionMedicalCenter);
		$form->bind($request);

		if($form->isValid()) {
			$institutionMedicalCenter->setStatus(InstitutionMedicalCenterStatus::INACTIVE);
			$em->persist($institutionMedicalCenter);
			$em->flush($institutionMedicalCenter);

			//// create event on add institutionMedicalCenter and dispatch
			// TODO - Need to check first if the action is ADD or EDIT then do the respective log action.
			$event = new CreateInstitutionMedicalCenterEvent($institutionMedicalCenter);
			$this->get('event_dispatcher')->dispatch(InstitutionMedicalCenterEvents::ON_ADD_INSTITUTION_MEDICAL_CENTER, $event);

			$request->getSession()->setFlash('success', 'Medical center has been saved!');

			if($request->get('submit') == 'Save') {
				$routeParams = array('institutionId' => $institutionId, 'imcId' => $institutionMedicalCenter->getId());

				return $this->redirect($this->generateUrl('admin_institution_medicalCenter_edit', $routeParams));
			} else {			
				return $this->redirect($this->generateUrl('admin_institution_medicalCenter_add', array('institutionId' => $institutionId)));
			}

		} else {

			if($institutionMedicalCenterId) {
				$formActionParams = array('institutionId' => $institutionId, 'imcId' => $institutionMedicalCenterId);
				$formAction = $this->generateUrl('admin_institution_medicalCenter_update', $formActionParams);
			} else {
				$formActionParams = array('institutionId' => $institutionId);
				$formAction = $this->generateUrl('admin_institution_medicalCenter_create', $formActionParams);
			}

			$params = array(
				'form' => $form->createView(),
				'institutionId' => $institutionId,
                'institutionMedicalCenter' => $institutionMedicalCenter,
				'formAction' => $formAction
			);

			return $this->render('AdminBundle:Institution:form.medicalCenter.html.twig', $params);
		}
	}
	

	/**
	 * 
	 * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_INSTITUTION')")
	 * @param int $institutionId
	 */ 
	public function updateMedicalCenterStatusAction()
	{
		$request = $this->getRequest();
		$em = $this->getDoctrine()->getEntityManager();
		$institutionMedicalCenter = $em->getRepository('InstitutionBundle:InstitutionMedicalCenter')->find($request->get('imcId'));

		if(!InstitutionMedicalCenterStatus::isValid($request->get('status'))) {
		    $request->getSession()->setFlash('error', 'Unable to update status. ' .$request->get('status')  . ' is invalid status value!' );

		    return $this->redirect($request->headers->get('referer'));
		}

		if ($institutionMedicalCenter) {
			$institutionMedicalCenter->setStatus($request->get('status'));
			$em->persist($institutionMedicalCenter);
			$em->flush($institutionMedicalCenter);

			//// create event on editInstitutionMedicalCenter and dispatch
			$event = new CreateInstitutionMedicalCenterEvent($institutionMedicalCenter);
			$this->get('event_dispatcher')->dispatch(InstitutionMedicalCenterEvents::ON_EDIT_INSTITUTION_MEDICAL_CENTER, $event);
		}

		$request->getSession()->setFlash('success', '"'.$institutionMedicalCenter->getMedicalCenter()->getName().'" status has been updated!');

		return $this->redirect($request->headers->get('referer'));
	}

	/**
	 * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_INSTITUTION')")
	 * @param int $institutionId
	 */
	public function addProcedureTypeAction($institutionId)
	{
		$request = $this->getRequest();

		$institution = $this->getDoctrine()->getRepository('InstitutionBundle:Institution')->find($institutionId);
		if (!$institution) {
			throw $this->createNotFoundException('Invalid institution');
		}

		$institutionMedicalCenter = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionMedicalCenter')->find($request->get('imcId'));

		if (!$institutionMedicalCenter) {
			throw $this->createNotFoundException('Invalid InstitutionMedicalCenter');
		}
		
		$institutionMedicalProcedureType = new InstitutionMedicalProcedureType();
		$institutionMedicalProcedureType->setInstitutionMedicalCenter($institutionMedicalCenter);
		$form = $this->createForm(new InstitutionMedicalProcedureTypeFormType(), $institutionMedicalProcedureType);

		return $this->render("AdminBundle:Institution:modalForm.medicalProcedureType.html.twig", array(
			'institution' => $institution,
			'institutionMedicalCenter' => $institutionMedicalCenter,
			'institutionMedicalProcedureType' => $institutionMedicalProcedureType,
			'form' => $form->createView(),
			'newProcedureType' => true
		));
	}

	/**
	 * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_INSTITUTION')")
	 * @param int $institutionId
	 */
	public function editProcedureTypeAction($institutionId)
	{
		$request = $this->getRequest();
		$institutionMedicalProcedureTypeId = $request->get('imptId');

		$institution = $this->getDoctrine()->getRepository('InstitutionBundle:Institution')->find($institutionId);
		if (!$institution) {
			throw $this->createNotFoundException('Invalid institution');
		}

		$institutionMedicalCenter = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionMedicalCenter')->find($request->get('imcId'));
		if (!$institutionMedicalCenter) {
			throw $this->createNotFoundException('Invalid InstitutionMedicalCenter');
		}

		$institutionMedicalProcedureType = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionMedicalProcedureType')->find($institutionMedicalProcedureTypeId);
		if (!$institutionMedicalProcedureType) {
			throw $this->createNotFoundException('Invalid InstitutionMedicalProcedureType');
		}
		
		$institutionMedicalProcedureType->setInstitutionMedicalCenter($institutionMedicalCenter);
		
		$form = $this->createForm(new InstitutionMedicalProcedureTypeFormType(),$institutionMedicalProcedureType);

		return $this->render("AdminBundle:Institution:modalForm.medicalProcedureType.html.twig", array(
			'institution' => $institution,
			'institutionMedicalCenter' => $institutionMedicalCenter,
			'institutionMedicalProcedureType' => $institutionMedicalProcedureType,
			'form' => $form->createView(),
			'newProcedureType' => false
		));
	}

	/**
	 * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_INSTITUTION')")
	 * @param int $institutionId
	 */	
	public function saveProcedureTypeAction($institutionId)
	{
		$request = $this->getRequest();

		if (!$request->isMethod('POST')) {
			return new Response('Unsupported method', 405);
		}

		$institution = $this->getDoctrine()->getRepository('InstitutionBundle:Institution')->find($institutionId);
		if (!$institution) {
			throw $this->createNotFoundException('Invalid Institution');
		}
		
		$institutionMedicalCenter = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionMedicalCenter')->find($request->get('imcId'));
		if (!$institutionMedicalCenter) {
			throw $this->createNotFoundException('Invalid InstitutionMedicanCenter');
		}

		if ($institutionMedicalProcedureTypeId = $request->get('imptId')) {
			$institutionMedicalProcedureType = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionMedicalProcedureType')->find($institutionMedicalProcedureTypeId);
			if (!$institutionMedicalProcedureType) {
				throw $this->createNotFoundException('Invalid InstitutionMedicalProcedureType');
			}
		}
		else {
			$institutionMedicalProcedureType = new InstitutionMedicalProcedureType();
		}
		
		$institutionMedicalProcedureType->setInstitutionMedicalCenter($institutionMedicalCenter);

		$form = $this->createForm(new InstitutionMedicalProcedureTypeFormType(), $institutionMedicalProcedureType);
		$form->bindRequest($request);

		if ($form->isValid()){
			$institutionMedicalProcedureType->setStatus(InstitutionMedicalProcedureType::STATUS_ACTIVE);
	
			$em = $this->getDoctrine()->getEntityManager();
			$em->persist($institutionMedicalProcedureType);
			$em->flush($institutionMedicalProcedureType);

			//// create event on add medicalProcedureTypes and dispatch
			// TODO - Need to check first if the action is ADD or EDIT then do the respective log action.
			$event = new CreateInstitutionMedicalProcedureTypeEvent($institutionMedicalProcedureType);
			$this->get('event_dispatcher')->dispatch(InstitutionMedicalProcedureTypeEvents::ON_ADD_INSTITUTION_MEDICAL_PROCEDURE_TYPE, $event);
			
			$request->getSession()->setFlash('success', 'Successfully saved institution procedure type.');
			return $this->redirect($this->generateUrl('admin_institution_medicalCenter_edit', array('institutionId' => $institutionId, 'imcId' => $institutionMedicalCenter->getId(), 'imptId' => $institutionMedicalProcedureType->getId())));
		}

		return $this->render('AdminBundle:Institution:form.medicalProcedureType.html.twig', array(
			'institution' => $institution,
			'institutionMedicalCenter' => $institutionMedicalCenter,
			'institutionMedicalProcedureType' => $institutionMedicalProcedureType,
			'form' => $form->createView(),
			'newProcedureType' => !$institutionMedicalProcedureTypeId,
		));
	}

	/**
	 * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_INSTITUTION')")
	 * @param int $institutionId
	 */
	public function addProcedureAction($institutionId)
	{
		$request = $this->getRequest();
		$em = $this->getDoctrine()->getEntityManager();

		$institution = $em->getRepository('InstitutionBundle:Institution')->find($institutionId);
		if (!$institution) {
			throw $this->createNotFoundException('Invalid Institution');
		}
		
		$institutionMedicalCenter = $em->getRepository('InstitutionBundle:InstitutionMedicalCenter')->find($request->get('imcId'));
		if (!$institutionMedicalCenter) {
			throw $this->createNotFoundException('Invalid InstitutionMedicanCenter');
		}

		$institutionMedicalProcedureType = $em->getRepository('InstitutionBundle:InstitutionMedicalProcedureType')->find($request->get('imptId'));
		if (!$institutionMedicalProcedureType) {
			throw $this->createNotFoundException('Invalid InstitutionMedicalProcedureType');
		}

		$institutionMedicalProcedure = new InstitutionMedicalProcedure();
		$institutionMedicalProcedure->setInstitutionMedicalProcedureType($institutionMedicalProcedureType);
		$form = $this->createForm(new InstitutionMedicalProcedureFormType(), $institutionMedicalProcedure);

		$formActionParams = array(
			'institutionId' => $institutionId,
			'imcId' => $institutionMedicalCenter->getId(),
			'imptId' => $institutionMedicalProcedureType->getId(),
		);

		$formAction = $this->generateUrl('admin_institution_medicalProcedure_create', $formActionParams);
		
		$params = array(
			'institutionId' => $institutionId,
			'institutionMedicalCenter' => $institutionMedicalCenter,
			'institutionMedicalProcedureType' => $institutionMedicalProcedureType,
			'formAction' => $formAction,
			'isNew' => true,
			'form' => $form->createView()
		);

		return $this->render('AdminBundle:Institution:modalForm.procedure.html.twig', $params);
	}

	/**
	 * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_INSTITUTION')")
	 * @param int $institutionId
	 */
	public function editProcedureAction($institutionId)
	{
		$request = $this->getRequest();
		$em = $this->getDoctrine()->getEntityManager();

		$institution = $em->getRepository('InstitutionBundle:Institution')->find($institutionId);
		if (!$institution) {
			throw $this->createNotFoundException('Invalid Institution');
		}
	
		$institutionMedicalCenter = $em->getRepository('InstitutionBundle:InstitutionMedicalCenter')->find($request->get('imcId'));
		if (!$institutionMedicalCenter) {
			throw $this->createNotFoundException('Invalid InstitutionMedicanCenter');
		}
	
		$institutionMedicalProcedureType = $em->getRepository('InstitutionBundle:InstitutionMedicalProcedureType')->find($request->get('imptId'));
		if (!$institutionMedicalProcedureType) {
			throw $this->createNotFoundException('Invalid InstitutionMedicalProcedureType');
		}
		
		$institutionMedicalProcedure = $em->getRepository('InstitutionBundle:InstitutionMedicalProcedure')->find($request->get('impId'));
		if (!$institutionMedicalProcedure) {
			throw $this->createNotFoundException('Invalid InstitutionMedicalProcedure');
		}

		$form = $this->createForm(new InstitutionMedicalProcedureFormType(), $institutionMedicalProcedure);	
		$formActionParams = array(
			'institutionId' => $institutionId,
			'imcId' => $institutionMedicalCenter->getId(),
			'imptId' => $institutionMedicalProcedureType->getId(),
			'impId' => $institutionMedicalProcedure->getId()
		);

		$formAction = $this->generateUrl('admin_institution_medicalProcedure_update', $formActionParams);
	
		$params = array(
			'institutionId' => $institutionId,
			'institutionMedicalCenter' => $institutionMedicalCenter,
			'institutionMedicalProcedureType' => $institutionMedicalProcedureType,
			'medicalProcedureName' => $institutionMedicalProcedure->getMedicalProcedure()->getName(),
			'formAction' => $formAction,
			'isNew' => false,
			'form' => $form->createView()
		);

		return $this->render('AdminBundle:Institution:modalForm.procedure.html.twig', $params);
	}

	/**
 	 * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_INSTITUTION')")
	 * @param integer $institutionId
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse
	 */
	public function saveProcedureAction($institutionId)
	{
		$request = $this->getRequest();
		$em = $this->getDoctrine()->getEntityManager();

		$institution = $em->getRepository('InstitutionBundle:Institution')->find($institutionId);
		if (!$institution) {
			throw $this->createNotFoundException('Invalid Institution');
		}
		
		$institutionMedicalCenter = $em->getRepository('InstitutionBundle:InstitutionMedicalCenter')->find($request->get('imcId'));
		if (!$institutionMedicalCenter) {
			throw $this->createNotFoundException('Invalid InstitutionMedicanCenter');
		}

		$institutionMedicalProcedureType = $em->getRepository('InstitutionBundle:InstitutionMedicalProcedureType')->find($request->get('imptId'));
		if (!$institutionMedicalProcedureType) {
			throw $this->createNotFoundException('Invalid InstitutionMedicalProcedureType');
		}

		$institutionMedicalProcedure = $em->getRepository('InstitutionBundle:InstitutionMedicalProcedure')->find($request->get('impId', 0));
		if (!$institutionMedicalProcedure) {
			$institutionMedicalProcedure = new InstitutionMedicalProcedure();
			$institutionMedicalProcedure->setInstitutionMedicalProcedureType($institutionMedicalProcedureType);
		}		

		$form = $this->createForm(new InstitutionMedicalProcedureFormType(), $institutionMedicalProcedure);
		$form->bindRequest($request);

		if ($form->isValid()) {
			$em->persist($institutionMedicalProcedure);
			$em->flush($institutionMedicalProcedure);
			
			//// create event on addInstitutionMedicalProcedure and dispatch
			// TODO - Need to check first if the action is ADD or EDIT then do the respective log action.
			$event = new CreateInstitutionMedicalProcedureEvent($institutionMedicalProcedure);
			$this->get('event_dispatcher')->dispatch(InstitutionMedicalProcedureEvents::ON_ADD_INSTITUTION_MEDICAL_PROCEDURE, $event);
			
			$request->getSession()->setFlash('success', "Successfully added a medical procedure to \"{$institutionMedicalProcedureType->getMedicalProcedureType()->getName()}\" procedure type.");

			return $this->redirect($this->generateUrl('admin_institution_medicalCenter_edit', array('institutionId' => $institutionId, 'imcId' => $institutionMedicalCenter->getId(), 'imptId' => $institutionMedicalProcedureType->getId())));
		}
	}

	/**
	 * 
	 * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_INSTITUTION')")
	 */
	public function updateProcedureStatusAction()
	{
		$result = false;

		$em = $this->getDoctrine()->getEntityManager();
		$institutionProcedure = $em->getRepository('InstitutionBundle:InstitutionMedicalProcedure')->find($this->getRequest()->get('impId'));
		
		if($institutionProcedure) {
			$status = $institutionProcedure->getStatus() == $institutionProcedure::STATUS_ACTIVE
			? $institutionProcedure::STATUS_INACTIVE
			: $institutionProcedure::STATUS_ACTIVE;
			
			$institutionProcedure->setStatus($status);
			$em->persist($institutionProcedure);
			$em->flush($institutionProcedure);
			
			//// create event on editInsitutionMedicalProcedure and dispatch
			$event = new CreateInstitutionMedicalProcedureEvent($institutionProcedure);
			$this->get('event_dispatcher')->dispatch(InstitutionMedicalProcedureEvents::ON_EDIT_INSTITUTION_MEDICAL_PROCEDURE, $event);
			
			$result = true;			
		}

		$response = new Response(json_encode($result));
		$response->headers->set('Content-Type', 'application/json');

		return $response;
	}
}