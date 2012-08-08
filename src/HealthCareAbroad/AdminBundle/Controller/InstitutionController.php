<?php

namespace HealthCareAbroad\AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Response;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use HealthCareAbroad\MedicalProcedureBundle\Entity\MedicalCenter;
use HealthCareAbroad\MedicalProcedureBundle\Entity\MedicalProcedureType;
use HealthCareAbroad\MedicalProcedureBundle\Entity\MedicalProcedure;

use HealthCareAbroad\InstitutionBundle\Entity\Institution;
use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter;
use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalProcedure;
use HealthCareAbroad\InstitutionBundle\Form\InstitutionMedicalProcedureType;

class InstitutionController extends Controller
{
	public function indexAction()
	{
		$institutions = $this->getDoctrine()->getEntityManager()->getRepository('InstitutionBundle:Institution')->findAll();

		return $this->render('AdminBundle:Institution:index.html.twig', array('institutions' => $institutions));
	}

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

	public function manageCentersAction($id)
	{
		$em = $this->getDoctrine()->getEntityManager();
		$institution = $em->getRepository('InstitutionBundle:Institution')->find($id);
		$institutionMedicalCenters = $institution->getMedicalCenters();

		$centerIdsWithProcedureType = $selectedCenterIds = array();
		foreach($institutionMedicalCenters as $each) {
			$medicalCenterId = $each->getMedicalCenter()->getId();
			$selectedCenterIds[] = $medicalCenterId;

			if(count($each->getMedicalProcedureType()))
				$centerIdsWithProcedureType[] = $medicalCenterId;
		}

		$request = $this->getRequest();
		if ('POST' == $request->getMethod()) {
			$newMedicalCenterIds = $request->get('centers', array());
			
			$this->get('services.institution')->updateInstitutionMedicalCenters($id, $newMedicalCenterIds, $centerIdsWithProcedureType);

			$selectedCenterIds = array_merge($newMedicalCenterIds, $centerIdsWithProcedureType);
			$request->getSession()->setFlash('notice', 'Institution Medical Centers has been updated!');
			//return $this->redirect($this->generateUrl('admin_institution_manageCenters', array('id'=>$id)));
		}

		$centers = $em->getRepository('MedicalProcedureBundle:MedicalCenter')->findByStatus(1);
		$params = array(
			'id' => $id,
			'centers' => $centers,
			'selectedCenterIds' => $selectedCenterIds,
			'centerIdsWithProcedureType' => $centerIdsWithProcedureType
		);
		return $this->render('AdminBundle:Institution:manage_centers.html.twig', $params);
	}
	
	public function manageProcedureTypesAction($id)
	{
		$request = $this->getRequest();
		$medicalCenterId = $request->get('medical_center_id');

		$em = $this->getDoctrine()->getEntityManager();
		$institution = $em->getRepository('InstitutionBundle:Institution')->find($id);
		$institutionMedicalCenters = $institution->getMedicalCenters();
		$medicalProcedureTypes = $em->getRepository('MedicalProcedureBundle:MedicalProcedureType')->findByStatus(1);

		$selectedInstitutionMedicalCenter = $medicalCenterId
			? $em->getRepository('InstitutionBundle:InstitutionMedicalCenter')->find($medicalCenterId)
			: $institutionMedicalCenters[0];

		$selectedProcedureTypeIds = array();
		$selectedProcedureType = $selectedInstitutionMedicalCenter->getMedicalProcedureType();
		foreach($selectedProcedureType as $each) {
			$selectedProcedureTypeIds[] = $each->getId();
		}

		$procedureTypeIdsWithProcedure = $em->getRepository('InstitutionBundle:InstitutionMedicalCenter')->getProcedureTypeIdsWithProcedure($selectedInstitutionMedicalCenter->getId());

		if ('POST' == $this->getRequest()->getMethod()) {
			$procedureTypeIds = $request->get('procedure_types', array());
			$this->get('services.institution')->updateInstitutionProcedureTypes($medicalCenterId, $procedureTypeIds, $procedureTypeIdsWithProcedure);

			$selectedProcedureTypeIds = array_merge($procedureTypeIds, $procedureTypeIdsWithProcedure);
			$request->getSession()->setFlash('notice', 'Institution Medical Procedure Types has been updated!');
		}

		$params = array(
			'id' => $id,
			'medicalCenters' => $institutionMedicalCenters,
			'selectedCenter' => $selectedInstitutionMedicalCenter,
			'procedureTypes' => $medicalProcedureTypes,
			'selectedProcedureTypeIds' => $selectedProcedureTypeIds,
			'procedureTypeIdsWithProcedure' => $procedureTypeIdsWithProcedure
		);
		return $this->render('AdminBundle:Institution:manage_proceduretypes.html.twig', $params);
	}
	
	public function manageProceduresAction($id)
	{
		$em = $this->getDoctrine()->getEntityManager();
		$institution = $em->getRepository('InstitutionBundle:Institution')->find($id);
		$filters = array('Active' => 1, 'Inactive' => 0, 'All' => -1);

		$selectedFilter = $this->getRequest()->get('filter', 'Active');
		if($selectedFilter != 'All') {
			$criteria['status'] = $filters[$selectedFilter];
		}

		$criteria['institution'] = $institution;
		$institutionProcedures = $em->getRepository('InstitutionBundle:InstitutionMedicalProcedure')->findBy($criteria);
		$form = $this->createForm(new InstitutionMedicalProcedureType($id), new InstitutionMedicalProcedure());

		$params = array(
			'id' => $id, 
			'filters' => $filters,
			'selectedFilter' => $selectedFilter,
			'institutionProcedures' => $institutionProcedures,
			'form' => $form->createView()
		);

		return $this->render('AdminBundle:Institution:manage_procedures.html.twig', $params);
	}
	
	// TODO - Move it to InstitutionMedicalProcedureController 
	public function updateProcedureStatusAction()
	{
		$em = $this->getDoctrine()->getEntityManager();
		$institutionMedicalProcedureId = $this->getRequest()->get('institution_medical_procedure_id');
		$procedure = $em->getRepository('InstitutionBundle:InstitutionMedicalProcedure')->find($institutionMedicalProcedureId);

		if($procedure) {
			$status = $procedure->getStatus() == InstitutionMedicalProcedure::$STATUS['active']
			? InstitutionMedicalProcedure::$STATUS['inactive']
			: InstitutionMedicalProcedure::$STATUS['active'];
		
			$procedure->setStatus($status);
			$em->persist($procedure);
			$em->flush($procedure);
			$result = true;
		}
		
		$response = new Response(json_encode($result));
		$response->headers->set('Content-Type', 'application/json');

		return $response;
	}
	
	// TODO - Move it to InstitutionMedicalProcedureController
	function saveMedicalProcedureAction($id)
	{
		$request = $this->getRequest();

		if('POST' == $request->getMethod()) {
			$data = $request->get('institutionMedicalProcedure');
			$em = $this->getDoctrine()->getEntityManager();

			$institution = $em->getRepository('InstitutionBundle:Institution')->find($id);
			$medicalProcedure = $em->getRepository('MedicalProcedureBundle:MedicalProcedure')->find($data['medical_procedure']);

			$instMedicalProcedure = new InstitutionMedicalProcedure();
			$instMedicalProcedure->setSlug('');
			$instMedicalProcedure->setStatus($data['status']);
			$instMedicalProcedure->setInstitution($institution);
			$instMedicalProcedure->setMedicalProcedure($medicalProcedure);			
			$instMedicalProcedure->setDescription($data['description']);
			$instMedicalProcedure->setDateCreated(new \DateTime());
			$instMedicalProcedure->setDateModified($instMedicalProcedure->getDateCreated());

			$em->persist($instMedicalProcedure);
			$em->flush($instMedicalProcedure);

			$request->getSession()->setFlash('notice', 'Institution Medical Procedure has been added!');			
		}

		return $this->redirect($this->generateUrl('admin_institution_manageProcedures', array('id'=>$id)));
	}
	
	// TODO - Move it to InstitutionMedicalProcedureController
	function loadProcedureTypesAction($medical_center_id)
	{
		$data = array();
		$em = $this->getDoctrine()->getEntityManager();
		$institutionMedicalCenter = $em->getRepository('InstitutionBundle:InstitutionMedicalCenter')->find($medical_center_id);
		
		if($institutionMedicalCenter && count($institutionMedicalCenter->getMedicalProcedureType())) {
			$procedureTypes = $institutionMedicalCenter->getMedicalProcedureType();
			foreach($procedureTypes as $each) {
				$data[] = array('id' => $each->getId(), 'name' => $each->getName());
			}
		}

		$response = new Response(json_encode($data));
		$response->headers->set('Content-Type', 'application/json');

		return $response;
	}
	
	// TODO - Move it to InstitutionMedicalProcedureController
	function loadProceduresAction($id, $procedure_type_id)
	{
		$data = array();
		$em = $this->getDoctrine()->getEntityManager();
		$procedureType = $em->getRepository('MedicalProcedureBundle:MedicalProcedureType')->find($procedure_type_id);
		$procedures = $em->getRepository('MedicalProcedureBundle:MedicalProcedure')->findByMedicalProcedureType($procedureType);

		$activeProcedureIds = $em->getRepository('InstitutionBundle:InstitutionMedicalProcedure')->getProcedureIdsByTypeId($id, $procedure_type_id);
		
		foreach($procedures as $each) {
			if(!in_array($each->getId(), $activeProcedureIds)) {
				$data[] = array('id' => $each->getId(), 'name' => $each->getName());				
			}
		}

		$response = new Response(json_encode($data));
		$response->headers->set('Content-Type', 'application/json');
	
		return $response;
	}
}