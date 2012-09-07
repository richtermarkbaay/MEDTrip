<?php

namespace HealthCareAbroad\AdminBundle\Controller;

use HealthCareAbroad\InstitutionBundle\Event\InstitutionMedicalProcedureEvents;

use HealthCareAbroad\InstitutionBundle\Event\CreateInstitutionMedicalProcedureEvent;

use HealthCareAbroad\InstitutionBundle\Event\InstitutionMedicalProcedureTypeEvents;

use HealthCareAbroad\InstitutionBundle\Event\CreateInstitutionMedicalProcedureTypeEvent;

use HealthCareAbroad\InstitutionBundle\Event\InstitutionMedicalCenterEvents;

use HealthCareAbroad\InstitutionBundle\Event\CreateInstitutionMedicalCenterEvent;

use HealthCareAbroad\AdminBundle\Events\MedicalCenterEvents;

use HealthCareAbroad\AdminBundle\Events\CreateMedicalCenterEvent;

use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Response;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use HealthCareAbroad\MedicalProcedureBundle\Entity\MedicalCenter;
//use HealthCareAbroad\MedicalProcedureBundle\Entity\MedicalProcedureType;
use HealthCareAbroad\MedicalProcedureBundle\Entity\MedicalProcedure;

use HealthCareAbroad\InstitutionBundle\Entity\Institution;
use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter;
use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalProcedure;
use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalProcedureType;

use HealthCareAbroad\InstitutionBundle\Form\InstitutionMedicalCenterType;
use HealthCareAbroad\InstitutionBundle\Form\InstitutionMedicalProcedureTypeFormType;
use HealthCareAbroad\InstitutionBundle\Form\InstitutionMedicalProcedureFormType;

use HealthCareAbroad\InstitutionBundle\Event\CreateInstitutionEvent;
use HealthCareAbroad\InstitutionBundle\Event\InstitutionEvents;
use JMS\SecurityExtraBundle\Annotation\PreAuthorize;

class InstitutionController extends Controller
{	
    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_VIEW_INSTITUTIONS')")
     */
	public function indexAction()
	{
		$request = $this->getRequest();

		$statusOptions = $this->get('services.institution')->getUpdateStatusOptions();
		$statusValues = $this->get('services.institution')->getStatusFilterOptions();

		$params = array('institutions' => $this->filteredResult, 'statusOptions' => $statusOptions, 'statusValues' => $statusValues);
		
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
	 * @param int $id
	 */
	public function updateStatusAction($institutionId)
	{
		$request = $this->getRequest();
		$em = $this->getDoctrine()->getEntityManager();
		$institution = $em->getRepository('InstitutionBundle:Institution')->find($institutionId);

		if($institution) {
			
			$institution->setStatus($request->get('status'));
			$em->persist($institution);
			$em->flush($institution);
			
			//TODO:: to create listener for the dispatch event of editInstitution Event
			$event = new CreateInstitutionEvent($institution, $user);
			$this->get('event_dispatcher')->dispatch(InstitutionEvents::ON_EDIT_INSTITUTION, $event);
					
		}

		$request->getSession()->setFlash('success', '"'.$institution->getName().'" has been updated!');
		return $this->redirect($this->generateUrl('admin_institution_index'));
	}

	/**
	 * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_INSTITUTION')")
	 * @param int $id
	 */
	public function manageCentersAction($institutionId)
	{
		$request = $this->getRequest();
		$status = $request->get('status');

		$em = $this->getDoctrine()->getEntityManager();
		$institution = $em->getRepository('InstitutionBundle:Institution')->find($institutionId);

		$criteria = $status == 'all' ? array() : array('status' => $status);
		$institutionMedicalCenters = $em->getRepository('InstitutionBundle:InstitutionMedicalCenter')->findBy($criteria);
		
		$params = array(
			'institutionId' => $institutionId,
			'selectedStatus' => $status,
			'institutionName' => $institution->getName(),
			'institutionMedicalCenters' => $this->filteredResult,
		);
		return $this->render('AdminBundle:Institution:manage_centers.html.twig', $params);
	}

	/**
	 *
	 * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_INSTITUTION')")
	 * @param int $id
	 */
	public function addMedicalCenterAction()
	{
		$request = $this->getRequest();
		$institutionId = $request->get('institutionId');

		$em = $this->getDoctrine()->getEntityManager();
		$institution = $em->getRepository('InstitutionBundle:Institution')->find($institutionId);

		$innstitutionMedicalCenter = new InstitutionMedicalCenter();
		$innstitutionMedicalCenter->setInstitution($institution);

		$form = $this->createForm(new InstitutionMedicalCenterType(), $innstitutionMedicalCenter);

		$formAction = $this->generateUrl('admin_institution_medicalCenter_create', array('institutionId' => $institutionId));

		$params = array(
			'institutionId' => $institutionId, 
			'institutionMedicalCenterId' => null,
			'formAction' => $formAction,
			'form' => $form->createView()
		);

		return $this->render('AdminBundle:Institution:form.medicalCenter.html.twig', $params);	
	}

	/**
	 *
	 * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_INSTITUTION')")
	 * @param int $id
	 */
	public function editMedicalCenterAction()
	{
		$request = $this->getRequest();
		$institutionId = $request->get('institutionId');
		$institutionMedicalCenterId = $request->get('imcId');

		$em = $this->getDoctrine()->getEntityManager();
		$institutionMedicalCenter = $em->getRepository('InstitutionBundle:InstitutionMedicalCenter')->find($institutionMedicalCenterId);

		$form = $this->createForm(new InstitutionMedicalCenterType(), $institutionMedicalCenter);

		$formActionParams = array('institutionId' => $institutionId, 'imcId' => $institutionMedicalCenterId);
		$formAction = $this->generateUrl('admin_institution_medicalCenter_update', $formActionParams);
		
		$params = array(
			'institutionId' => $institutionId,
			'institutionMedicalCenterId' => $institutionMedicalCenterId,
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
			$institutionMedicalCenter = new InstitutionMedicalCenter;
			$institutionMedicalCenter->setInstitution($institution);
		}

		$form = $this->createForm(new InstitutionMedicalCenterType(), $institutionMedicalCenter);
		$form->bind($request);

		if($form->isValid()) {
			$em->persist($institutionMedicalCenter);
			$em->flush($institutionMedicalCenter);

			//// create event on add institutionMedicalCenter and dispatch
			$event = new CreateInstitutionMedicalCenterEvent($institutionMedicalCenter);
			$this->get('event_dispatcher')->dispatch(InstitutionMedicalCenterEvents::ON_ADD_INSTITUTION_MEDICAL_CENTER, $event);
			
			$request->getSession()->setFlash('success', 'Medical center has been added!');
			return $this->redirect($this->generateUrl('admin_institution_manageCenters', array('institutionId'=>$institutionId)));
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
				'institutionMedicalCenterId' => $institutionMedicalCenterId,
				'formAction' => $formAction
			);

			return $this->render('AdminBundle:Institution:form.medicalCenter.html.twig', $params);
		}


	}
	/**
	 *
	 * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_INSTITUTION')")
	 * @param int $id
	 */
	public function updateMedicalCenterStatusAction()
	{
		$request = $this->getRequest();
		$result = false;
		$em = $this->getDoctrine()->getEntityManager();

		$institutionMedicalCenter = $em->getRepository('InstitutionBundle:InstitutionMedicalCenter')->find($request->get('imcId'));
	
		if ($institutionMedicalCenter) {
			$status = $institutionMedicalCenter->getStatus()
					? $institutionMedicalCenter::STATUS_INACTIVE
					: $institutionMedicalCenter::STATUS_ACTIVE;

			$institutionMedicalCenter->setStatus($status);
			$em->persist($institutionMedicalCenter);
			$em->flush($institutionMedicalCenter);
			
			//// create event on editInstitutionMedicalCenter and dispatch
			$event = new CreateInstitutionMedicalCenterEvent($institutionMedicalCenter);
			$this->get('event_dispatcher')->dispatch(InstitutionMedicalCenterEvents::ON_EDIT_INSTITUTION_MEDICAL_CENTER, $event);
			
			$result = true;
		}

		$response = new Response(json_encode($result));
		$response->headers->set('Content-Type', 'application/json');
	
		return $response;
	}

	/**
	 * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_INSTITUTION')")
	 * @param int $id
	 */
	public function manageProcedureTypesAction($institutionId)
	{
		$em = $this->getDoctrine()->getEntityManager();
		$institution = $em->getRepository('InstitutionBundle:Institution')->find($institutionId);
		$institutionMedicalCenter = $em->getRepository('InstitutionBundle:InstitutionMedicalCenter')->find($this->getRequest()->get('imcId'));

		$params = array(
			'institution' => $institution,
			'institutionMedicalCenter' => $institutionMedicalCenter,
			'medicalProcedureTypes' => $this->filteredResult
		);
		
		return $this->render('AdminBundle:Institution:manage_proceduretypes.html.twig', $params);
	}

	/**
	 * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_INSTITUTION')")
	 * @param int $id
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

		return $this->render('AdminBundle:Institution:form.medicalProcedureType.html.twig', array(
			'institution' => $institution,
			'institutionMedicalCenter' => $institutionMedicalCenter,
			'institutionMedicalProcedureType' => $institutionMedicalProcedureType,
			'form' => $form->createView(),
			'newProcedureType' => true
		));
	}

	/**
	 * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_INSTITUTION')")
	 * @param int $id
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
	
		return $this->render('AdminBundle:Institution:form.medicalProcedureType.html.twig', array(
			'institution' => $institution,
			'institutionMedicalCenter' => $institutionMedicalCenter,
			'institutionMedicalProcedureType' => $institutionMedicalProcedureType,
			'form' => $form->createView(),
			'newProcedureType' => false
		));
	}

	/**
	 * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_INSTITUTION')")
	 * @param int $id
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
			$event = new CreateInstitutionMedicalProcedureTypeEvent($institutionMedicalProcedureType);
			$this->get('event_dispatcher')->dispatch(InstitutionMedicalProcedureTypeEvents::ON_ADD_INSTITUTION_MEDICAL_PROCEDURE_TYPE, $event);
			
			$request->getSession()->setFlash('success', 'Successfully saved institution procedure type.');
			return $this->redirect($this->generateUrl('admin_institution_manageProcedureTypes', array('institutionId' => $institutionId, 'imcId' => $institutionMedicalCenter->getId())));
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
		return $this->render('AdminBundle:Institution:form.procedure.html.twig', $params);
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
			'impId' => $institutionMedicalProcedure->getId(),
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
		return $this->render('AdminBundle:Institution:form.procedure.html.twig', $params);
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
			$event = new CreateInstitutionMedicalProcedureEvent($institutionMedicalProcedure);
			$this->get('event_dispatcher')->dispatch(InstitutionMedicalProcedureEvents::ON_ADD_INSTITUTION_MEDICAL_PROCEDURE, $event);
			
			$request->getSession()->setFlash('success', "Successfully added a medical procedure to \"{$institutionMedicalProcedureType->getMedicalProcedureType()->getName()}\" procedure type.");

			return $this->redirect($this->generateUrl('admin_institution_manageProcedureTypes', array('institutionId' => $institutionId, 'imcId' => $institutionMedicalCenter->getId()) ));
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