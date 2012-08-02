<?php

namespace HealthCareAbroad\AdminBundle\Controller;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use HealthCareAbroad\InstitutionBundle\Entity\Institution;
use HealthCareAbroad\MedicalProcedureBundle\Entity\MedicalCenter;
use HealthCareAbroad\MedicalProcedureBundle\Entity\MedicalProcedureType;
use HealthCareAbroad\MedicalProcedureBundle\Entity\MedicalProcedure;

class InstitutionController extends Controller
{
	public function indexAction()
	{
		$institutions = $this->getDoctrine()->getEntityManager()->getRepository('InstitutionBundle:Institution')->findAll();

		return $this->render('AdminBundle:Institution:index.html.twig', array('institutions' => $institutions));
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

		if ('POST' == $this->getRequest()->getMethod()) {
			$newMedicalCenterIds = $this->getRequest()->get('centers', array());
			
			$this->get('services.institution')->updateInstitutionMedicalCenters($id, $newMedicalCenterIds, $centerIdsWithProcedureType);

			$selectedCenterIds = array_merge($newMedicalCenterIds, $centerIdsWithProcedureType);
		}

		$centers = $em->getRepository('MedicalProcedureBundle:MedicalCenter')->findByStatus(1);
		$params = array('id' => $id, 'centers' => $centers, 'selectedCenterIds' => $selectedCenterIds, 'centerIdsWithProcedureType' => $centerIdsWithProcedureType);
		return $this->render('AdminBundle:Institution:manage_centers.html.twig', $params);
	}
	
	public function manageProcedureTypesAction($id)
	{
	}
	
	public function manageProcedures($id)
	{
	}
	
	public function updateInstitionCenters($id, $medicalCenterIds)
	{
		
	}
}