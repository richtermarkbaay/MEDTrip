<?php 

namespace HealthCareAbroad\InstitutionBundle\Controller;

use Symfony\Component\HttpFoundation\Response;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class MedicalProcedureTypeController extends Controller
{

	function loadProceduresAction()
	{
		$request = $this->getRequest();
		$institutionId = $request->get('institution_id', $this->get('session')->get('institutionId'));
		$procedureTypeId = $request->get('procedure_type_id');

		$em = $this->getDoctrine()->getEntityManager();
		$procedureType = $em->getRepository('MedicalProcedureBundle:MedicalProcedureType')->find($procedureTypeId);
	
		$criteria = array('medicalProcedureType' => $procedureType, 'status' => 1);
		$procedures = $em->getRepository('MedicalProcedureBundle:MedicalProcedure')->findBy($criteria);
	
		$activeProcedureIds = $em->getRepository('InstitutionBundle:InstitutionMedicalProcedure')->getProcedureIdsByTypeId($institutionId, $procedureTypeId);

		$data = array();
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