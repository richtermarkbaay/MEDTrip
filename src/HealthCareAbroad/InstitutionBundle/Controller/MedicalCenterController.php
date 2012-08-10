<?php 

namespace HealthCareAbroad\InstitutionBundle\Controller;

use Symfony\Component\HttpFoundation\Response;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class MedicalCenterController extends Controller
{

	function loadProcedureTypesAction()
	{
		$data = array();
		$em = $this->getDoctrine()->getEntityManager();

		$centerId = $this->getRequest()->get('medical_center_id');
		$institutionMedicalCenter = $em->getRepository('InstitutionBundle:InstitutionMedicalCenter')->find($centerId);

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
}