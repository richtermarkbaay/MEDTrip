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
		$institutions = $this->getDoctrine()->getEntityManager()->getRepository('InstitutionBundle:Institution')->findAll();

		return $this->render('AdminBundle:Institution:index.html.twig', array('institutions' => $institutions));
	}
	
	/**
	 * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_INSTITUTION')")
	 */
	public function viewAction(Request $request)
	{
	    $institution = $this->getDoctrine()->getRepository('InstitutionBundle:Institution')->find($request->get('id', 0));
	    if (!$institution) {
	        throw $this->createNotFoundException('Invalid institution');
	    }
	    
	    return $this->render('AdminBundle:Institution:view.html.twig', array('institution' => $institution));
	}
	
	/**
	 * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_DELETE_INSTITUTION')")
	 * @param int $id
	 */
	public function updateStatusAction($id)
	{
		$result = false;
		$em = $this->getDoctrine()->getEntityManager();
		$institution = $em->getRepository('InstitutionBundle:Institution')->find($id);

		if($institution) {
			$status = $institution->getStatus() == Institution::$STATUS['active']
			? Institution::$STATUS['inactive']
			: Institution::$STATUS['active'];

			$institution->setStatus($status);
			$em->persist($institution);
			$em->flush($institution);
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
	public function manageCentersAction($id)
	{
		$em = $this->getDoctrine()->getEntityManager();
		$institution = $em->getRepository('InstitutionBundle:Institution')->find($id);

		$params = array(
			'id' => $id,
			'institutionName' => $institution->getName(),
			'institutionMedicalCenters' => $institution->getInstitutionMedicalCenters(),
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
		$id = $request->get('id');
	
		$em = $this->getDoctrine()->getEntityManager();
		$institution = $em->getRepository('InstitutionBundle:Institution')->find($id);

		$newInstitutionMedicalCenter = new InstitutionMedicalCenter;
		$newInstitutionMedicalCenter->setInstitution($institution);
		$form = $this->createForm(new InstitutionMedicalCenterType(), $newInstitutionMedicalCenter);

		$params = array(
				'id' => $id,
				'form' => $form->createView()
		);
		return $this->render('AdminBundle:Institution:form.medicalCenter.html.twig', $params);
	
	}

	/**
	 *
	 * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_INSTITUTION')")
	 * @param int $id
	 */
	public function saveMedicalCenterAction($id)
	{
		$request = $this->getRequest();
		
		if('POST' != $request->getMethod()) {
			
		} else {
			$em = $this->getDoctrine()->getEntityManager();
			$institution = $em->getRepository('InstitutionBundle:Institution')->find($id);

			$newInstitutionMedicalCenter = new InstitutionMedicalCenter;
			$newInstitutionMedicalCenter->setInstitution($institution);
			$form = $this->createForm(new InstitutionMedicalCenterType(), $newInstitutionMedicalCenter);
			$form->bind($request);
			
			if($form->isValid()) {
				$em->persist($newInstitutionMedicalCenter);
				$em->flush($newInstitutionMedicalCenter);
				
				$request->getSession()->setFlash('success', 'Medical center has been added!');
			}
		}

		return $this->redirect($this->generateUrl('admin_institution_manageCenters', array('id'=>$id)));
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
		
		$institution = $em->getRepository('InstitutionBundle:Institution')->find($request->get('id'));
		$medicalCenter = $em->getRepository('MedicalProcedureBundle:MedicalCenter')->find($request->get('medical_center_id'));
		
		$criteria = array('institution' => $institution, 'medicalCenter'=> $medicalCenter);
		$institutionMedicalCenter = $em->getRepository('InstitutionBundle:InstitutionMedicalCenter')->findOneBy($criteria);
	
		if ($institutionMedicalCenter) {
			$institutionMedicalCenter->setStatus($institutionMedicalCenter->getStatus() ? 0 : 1);
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
	public function manageProcedureTypesAction($id)
	{
		$em = $this->getDoctrine()->getEntityManager();
		$institution = $em->getRepository('InstitutionBundle:Institution')->find($id);

		$request = $this->getRequest();
		$medicalCenterId = $request->get('medical_center_id');
		$institutionMedicalCenters = $institution->getInstitutionMedicalCenters();

		$criteria = array('medicalProcedureType' => 1);
 		$medicalProcedureTypes = $em->getRepository('InstitutionBundle:InstitutionMedicalProcedureType')->findAll();
 
		$params = array('id' => $id,'institutionName'=>$institution->getName(), 'medicalProcedureTypes' => $medicalProcedureTypes);
		return $this->render('AdminBundle:Institution:manage_listings.html.twig', $params);
	}

	/**
	 * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_INSTITUTION')")
	 * @param int $id
	 */
	public function addProcedureTypeAction($id)
	{
		$request = $this->getRequest();

		$institution = $this->getDoctrine()->getRepository('InstitutionBundle:Institution')->find($id);
		if (!$institution) {
			throw $this->createNotFoundException('Invalid institution');
		}

		$institutionMedicalProcedureType = new InstitutionMedicalProcedureType();
		$form = $this->createForm(new InstitutionMedicalProcedureTypeFormType(),$institutionMedicalProcedureType, array('institution' => $institution));
	
		return $this->render('AdminBundle:Institution:form.medicalProcedureType.html.twig', array(
			'id' => $id,
			'form' => $form->createView(),
			'institutionMedicalProcedureType' => $institutionMedicalProcedureType,
			'newListing' => true
		));
	}

	/**
	 * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_INSTITUTION')")
	 * @param int $id
	 */
	public function editProcedureTypeAction($id)
	{
		$request = $this->getRequest();
		$institutionMedicalProcedureTypeId = $request->get('institution_medical_procedure_type_id');

		$institution = $this->getDoctrine()->getRepository('InstitutionBundle:Institution')->find($id);
		if (!$institution) {
			throw $this->createNotFoundException('Invalid institution');
		}

		$institutionMedicalProcedureType = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionMedicalProcedureType')->find($institutionMedicalProcedureTypeId);
		if (!$institutionMedicalProcedureType) {
			throw $this->createNotFoundException('Invalid InstitutionMedicalProcedureType');
		}

		$form = $this->createForm(new InstitutionMedicalProcedureTypeFormType(),$institutionMedicalProcedureType, array('institution' => $institution));
	
		return $this->render('AdminBundle:Institution:form.medicalProcedureType.html.twig', array(
			'id' => $id,
			'form' => $form->createView(),
			'institutionMedicalProcedureType' => $institutionMedicalProcedureType,
			'newListing' => false
		));
	}

	/**
	 * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_INSTITUTION')")
	 * @param int $id
	 */	
	public function saveProcedureTypeAction($id)
	{
		$request = $this->getRequest();

		if (!$request->isMethod('POST')) {
			return new Response('Unsupported method', 405);
		}

		$institution = $this->getDoctrine()->getRepository('InstitutionBundle:Institution')->find($id);
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

			$request->getSession()->setFlash('success', 'Successfully saved listing.');
			return $this->redirect($this->generateUrl('admin_institution_editListing', array('id' => $id, 'institution_medical_procedure_type_id' => $institutionMedicalProcedureType->getId())));
		}

		return $this->render('AdminBundle:Institution:form.medicalProcedureType.html.twig', array(
			'id' => $id,
			'institution_medical_procedure_type_id' => $institutionMedicalProcedureTypeId,
			'form' => $form->createView(),
			'institutionMedicalProcedureType' => $institutionMedicalProcedureType,
			'newListing' => $institutionMedicalProcedureTypeId == 0,
		));
	}

	/**
	 * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_INSTITUTION')")
	 * @param int $id
	 */
	public function addProcedureAction()
	{
		$request = $this->getRequest();
		$id = $request->get('id');
		$instMedicalProcedureTypeId = $request->get('institution_medical_procedure_type_id');

		$em = $this->getDoctrine()->getEntityManager();
		$institution = $em->getRepository('InstitutionBundle:Institution')->find($id);
		$instMedicalProcedureType = $em->getRepository('InstitutionBundle:InstitutionMedicalProcedureType')->find($instMedicalProcedureTypeId);

		$institutionMedicalProcedure = new InstitutionMedicalProcedure();
		
		$form = $this->createForm(new InstitutionMedicalProcedureFormType(), $institutionMedicalProcedure, array('institutionMedicalProcedureType' => $instMedicalProcedureType));
		$params = array(
			'id' => $id,
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
			$request->getSession()->setFlash('success', "Successfully added a medical procedure to \"{$institutionMedicalProcedureType->getMedicalProcedureType()->getName()}\" listing.");

			return $this->redirect($this->generateUrl('admin_institution_manageListings', array('id' => $request->get('id'))));
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