<?php 

namespace HealthCareAbroad\InstitutionBundle\Controller;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalProcedureType;

use HealthCareAbroad\InstitutionBundle\Form\InstitutionMedicalProcedureTypeFormType;

use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Response;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class MedicalProcedureTypeController extends Controller
{
    
    public function indexAction(Request $request)
    {
        $institution = $this->getDoctrine()->getRepository('InstitutionBundle:Institution')->find($request->getSession()->get('institutionId'));
        if (!$institution) {
            throw $this->createNotFoundException('Invalid institution');
        }
        
        return $this->render('InstitutionBundle:MedicalProcedureType:index.html.twig');
    }
    
    public function addAction(Request $request)
    {
        $institution = $this->getDoctrine()->getRepository('InstitutionBundle:Institution')->find($request->getSession()->get('institutionId'));
        if (!$institution) {
            throw $this->createNotFoundException('Invalid institution');
        }
        
        $form = $this->createForm(new InstitutionMedicalProcedureTypeFormType(),new InstitutionMedicalProcedureType(), array('institution' => $institution));
        
        return $this->render('InstitutionBundle:MedicalProcedureType:form.html.twig', array(
            'form' => $form->createView(),
            'institutionMedicalProcedureType' => $institutionMedicalProcedureType,
            'newListing' => true
        ));
    }
    
    public function saveAction(Request $request)
    {
        if (!$request->isMethod('POST')) {
            return new Response('Unsupported method', 405);
        }
        
        $institution = $this->getDoctrine()->getRepository('InstitutionBundle:Institution')->find($request->getSession()->get('institutionId'));
        if (!$institution) {
            throw $this->createNotFoundException('Invalid institution');
        }
        
        if ($id = $request->get('id', 0)) {
            $institutionMedicalProcedureType = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionMedicalProcedureType')->find($id);
            if (!$institutionMedicalProcedureType) {
                throw $this->createNotFoundException('Invalid InstitutionMedicalProcedureType');
            }
        }
        else {
            $institutionMedicalProcedureType = new InstitutionMedicalProcedureType();
        }
        
        $form = $this->createForm(new InstitutionMedicalProcedureTypeFormType(), $institutionMedicalProcedureType)
    }

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