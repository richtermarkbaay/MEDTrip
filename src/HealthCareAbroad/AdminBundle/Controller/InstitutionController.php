<?php

namespace HealthCareAbroad\AdminBundle\Controller;

use HealthCareAbroad\InstitutionBundle\Form\InstitutionMedicalCenterType;

use HealthCareAbroad\MedicalProcedureBundle\Form\MedicalProcedureType;

use Symfony\Component\HttpFoundation\Response;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use HealthCareAbroad\MedicalProcedureBundle\Entity\MedicalCenter;
//use HealthCareAbroad\MedicalProcedureBundle\Entity\MedicalProcedureType;
use HealthCareAbroad\MedicalProcedureBundle\Entity\MedicalProcedure;

use HealthCareAbroad\InstitutionBundle\Entity\Institution;
use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter;
use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalProcedure;
use HealthCareAbroad\InstitutionBundle\Form\InstitutionMedicalProcedureType;
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
	 * 
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
	 * 
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
	 * 
	 * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_INSTITUTION')")
	 * @param int $id
	 */
	public function manageListingsAction($id)
	{
		$request = $this->getRequest();
		$em = $this->getDoctrine()->getEntityManager();

		$institution = $em->getRepository('InstitutionBundle:Institution')->find($id);

		$medicalCenterId = $request->get('medical_center_id');
		$institutionMedicalCenters = $institution->getInstitutionMedicalCenters();

		$criteria = array('medicalProcedureType' => 1);
 		$medicalProcedureTypes = $em->getRepository('InstitutionBundle:InstitutionMedicalProcedureType')->findAll();
 
		$params = array('id' => $id, 'medicalProcedureTypes' => $medicalProcedureTypes);
		//return $this->render('AdminBundle:Institution:manage_listings.html.twig', $params);
		return $this->render('AdminBundle:Institution:manage_listings.html.twig', $params);
	}
	
	/**
	 * 
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
		
		$form = $this->createForm(new InstitutionMedicalProcedureType(), new InstitutionMedicalProcedure());

		$params = array(
			'id' => $id,
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
	public function editProceduresAction($id)
	{
	}

	/**
	 * 
	 * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_INSTITUTION')")
	 */
	public function updateProcedureStatusAction()
	{
		$em = $this->getDoctrine()->getEntityManager();
		$id = $this->getRequest()->get('institution_medical_procedure_id');
		$result = $em->getRepository('InstitutionBundle:InstitutionMedicalProcedure')->updateStatus($id);

		$response = new Response(json_encode($result));
		$response->headers->set('Content-Type', 'application/json');
		return $response;
	}
	
	/**
	 * 
	 * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_INSTITUTION')")
	 */
	function saveMedicalProcedureAction($id)
	{
		$request = $this->getRequest();

		if('POST' == $request->getMethod()) {
			$data = $request->get('institutionMedicalProcedure');
			$em = $this->getDoctrine()->getEntityManager();

			try {
				$institution = $em->getRepository('InstitutionBundle:Institution')->find($id);
				$criteria = array('id' => $data['medical_procedure'], 'status' => MedicalProcedure::$STATUS['active']);
				$medicalProcedure = $em->getRepository('MedicalProcedureBundle:MedicalProcedure')->findOneBy($criteria);

				$instMedicalProcedure = new InstitutionMedicalProcedure();
				$instMedicalProcedure->setStatus($data['status']);
				$instMedicalProcedure->setInstitution($institution);
				$instMedicalProcedure->setMedicalProcedure($medicalProcedure);
				$instMedicalProcedure->setDescription($data['description']);
				$instMedicalProcedure->setDateCreated(new \DateTime());
				$instMedicalProcedure->setDateModified($instMedicalProcedure->getDateCreated());

				$em->persist($instMedicalProcedure);
				$em->flush($instMedicalProcedure);

				$request->getSession()->setFlash('success', 'Institution Medical Procedure has been added!');

			} catch(\ErrorException $e) {
				$request->getSession()->setFlash('error', "Medical Procedure does not exists or already inactive!");
			}
		}

		return $this->redirect($this->generateUrl('admin_institution_manageProcedures', array('id'=>$id)));
	}
}