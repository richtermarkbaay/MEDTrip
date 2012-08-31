<?php

namespace HealthCareAbroad\AdminBundle\Controller;

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

		$params = array(
			'institutionId' => $institutionId,
			'institutionName' => $institution->getName(),
			'institutionMedicalCenterId' => $this->getRequest()->get('imcId'),
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
			'institutionId' => $institutionId,
			'medicalCenterName' => $institutionMedicalCenter->getMedicalCenter()->getName(),
			'institutionMedicalCenterId' => $institutionMedicalCenter->getId(),
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
		$institutionMedicalProcedureTypeId = $request->get('institution_medical_procedure_type_id');

		$institution = $this->getDoctrine()->getRepository('InstitutionBundle:Institution')->find($institutionId);
		if (!$institution) {
			throw $this->createNotFoundException('Invalid institution');
		}

		$institutionMedicalProcedureType = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionMedicalProcedureType')->find($institutionMedicalProcedureTypeId);
		if (!$institutionMedicalProcedureType) {
			throw $this->createNotFoundException('Invalid InstitutionMedicalProcedureType');
		}

		$form = $this->createForm(new InstitutionMedicalProcedureTypeFormType(),$institutionMedicalProcedureType, array('institution' => $institution));
	
		return $this->render('AdminBundle:Institution:form.medicalProcedureType.html.twig', array(
			'institutionId' => $institutionId,
			'form' => $form->createView(),
			'institutionMedicalProcedureType' => $institutionMedicalProcedureType,
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
			throw $this->createNotFoundException('Invalid institution');
		}

		if ($institutionMedicalProcedureTypeId = $request->get('institution_medical_procedure_type_id')) {
			$institutionMedicalProcedureType = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionMedicalProcedureType')->find($institutionMedicalProcedureTypeId);
			if (!$institutionMedicalProcedureType) {
				throw $this->createNotFoundException('Invalid InstitutionMedicalProcedureType');
			}
		}
		else {
			$institutionMedicalProcedureType = new InstitutionMedicalProcedureType();
		}

		$form = $this->createForm(new InstitutionMedicalProcedureTypeFormType(), $institutionMedicalProcedureType, array('institution' => $institution));
		$form->bindRequest($request);

		if ($form->isValid()){
			$institutionMedicalProcedureType->setInstitution($institution);
			$institutionMedicalProcedureType->setStatus(InstitutionMedicalProcedureType::STATUS_ACTIVE);
	
			$em = $this->getDoctrine()->getEntityManager();
			$em->persist($institutionMedicalProcedureType);
			$em->flush($institutionMedicalProcedureType);

			$request->getSession()->setFlash('success', 'Successfully saved institution procedure type.');
			return $this->redirect($this->generateUrl('admin_institution_editProcedureType', array('institutionId' => $institutionId, 'institution_medical_procedure_type_id' => $institutionMedicalProcedureType->getId())));
		}

		return $this->render('AdminBundle:Institution:form.medicalProcedureType.html.twig', array(
			'institutionId' => $institutionId,
			'institution_medical_procedure_type_id' => $institutionMedicalProcedureTypeId,
			'form' => $form->createView(),
			'institutionMedicalProcedureType' => $institutionMedicalProcedureType,
			'newProcedureType' => $institutionMedicalProcedureTypeId == 0,
		));
	}

	/**
	 * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_INSTITUTION')")
	 * @param int $id
	 */
	public function addProcedureAction()
	{
		$request = $this->getRequest();
		$id = $request->get('institutionId');
		$instMedicalProcedureTypeId = $request->get('institution_medical_procedure_type_id');

		$em = $this->getDoctrine()->getEntityManager();
		$institution = $em->getRepository('InstitutionBundle:Institution')->find($institutionId);
		$instMedicalProcedureType = $em->getRepository('InstitutionBundle:InstitutionMedicalProcedureType')->find($instMedicalProcedureTypeId);

		$institutionMedicalProcedure = new InstitutionMedicalProcedure();
		
		$form = $this->createForm(new InstitutionMedicalProcedureFormType(), $institutionMedicalProcedure, array('institutionMedicalProcedureType' => $instMedicalProcedureType));
		$params = array(
			'institutionId' => $institutionId,
			'institution_medical_procedure_type_id' => $instMedicalProcedureTypeId,
			'procedureTypeName' => $instMedicalProcedureType->getMedicalProcedureType()->getName(),
			'medicalCenterName' => $instMedicalProcedureType->getMedicalProcedureType()->getMedicalCenter()->getName(),
			'form' => $form->createView()
		);
		return $this->render('AdminBundle:Institution:form.procedure.html.twig', $params);
		
	}

	/**
	 *
	 * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_INSTITUTION')")
	 * @param int $id
	 */
	public function saveProcedureAction()
	{
		$request = $this->getRequest();
		$instMedicalProcedueTypeId = $this->getRequest()->get('institution_medical_procedure_type_id');
		

		$institutionMedicalProcedureType = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionMedicalProcedureType')->find($instMedicalProcedueTypeId);
		if (!$institutionMedicalProcedureType) {
			throw $this->createNotFoundException('Invalid InstitutionMedicalProcedureType');
		}

		$institutionMedicalProcedure = new InstitutionMedicalProcedure();
		$form = $this->createForm(new InstitutionMedicalProcedureFormType(), $institutionMedicalProcedure, array('institutionMedicalProcedureType' => $institutionMedicalProcedureType));
		$form->bindRequest($request);
		
		if ($form->isValid()) {
			$institutionMedicalProcedure->setInstitutionMedicalProcedureType($institutionMedicalProcedureType);
			$em = $this->getDoctrine()->getEntityManager();
			$em->persist($institutionMedicalProcedure);
			$em->flush($institutionMedicalProcedure);
			$request->getSession()->setFlash('success', "Successfully added a medical procedure to \"{$institutionMedicalProcedureType->getMedicalProcedureType()->getName()}\" procedure type.");

			return $this->redirect($this->generateUrl('admin_institution_manageProcedureTypes', array('id' => $request->get('institutionId'))));
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
		$institutionProcedureTypeId = $this->getRequest()->get('institution_medical_procedure_type_id');
		$institutionProcedureType = $em->getRepository('InstitutionBundle:InstitutionMedicalProcedureType')->find($institutionProcedureTypeId);
		
		$medicalProcedureId = $this->getRequest()->get('medical_procedure_id');
		$medicalProcedure = $em->getRepository('MedicalProcedureBundle:MedicalProcedure')->find($medicalProcedureId);
		
		$criteria = array('institutionMedicalProcedureType' => $institutionProcedureType, 'medicalProcedure' => $medicalProcedure);
		
		$institutionProcedure = $em->getRepository('InstitutionBundle:InstitutionMedicalProcedure')->findOneBy($criteria);
		
		if($institutionProcedure) {

			$status = $institutionProcedure->getStatus() == $institutionProcedure::STATUS_ACTIVE
			? $institutionProcedure::STATUS_INACTIVE
			: $institutionProcedure::STATUS_ACTIVE;
			
			$institutionProcedure->setStatus($status);
			$em->persist($institutionProcedure);
			$em->flush($institutionProcedure);
			$result = true;			
		}


		$response = new Response(json_encode($result));
		$response->headers->set('Content-Type', 'application/json');
		return $response;
	}
}