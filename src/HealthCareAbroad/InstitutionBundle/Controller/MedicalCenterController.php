<?php 

namespace HealthCareAbroad\InstitutionBundle\Controller;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter;

use HealthCareAbroad\InstitutionBundle\Form\InstitutionMedicalCenterType;

use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Response;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class MedicalCenterController extends Controller
{
    
    public function indexAction(Request $request)
    {
        $institutionRepository = $this->getDoctrine()->getRepository('InstitutionBundle:Institution'); 
        $institution = $institutionRepository->find($request->getSession()->get('institutionId'));
        
        if (!$institution) {
            throw $this->createNotFoundException('Invalid institution');
        }
        $institutionMedicalCenters = $institutionRepository->getActiveInstitutionMedicalCenters($institution);
        
        $newInstitutionMedicalCenter = new InstitutionMedicalCenter();
        $newInstitutionMedicalCenter->setInstitution($institution);
        $form = $this->createForm(new InstitutionMedicalCenterType(), $newInstitutionMedicalCenter);
        
        return $this->render('InstitutionBundle:MedicalCenter:index.html.twig', array(
            'institutionMedicalCenters' => $institutionMedicalCenters,
            'form' => $form->createView()
        ));
    }
    
    public function saveAction(Request $request)
    {
        if (!$request->isMethod('POST')) {
            return $this->_errorResponse("POST is the only allowed method", 405);
        }
        
        $institution = $this->getDoctrine()->getRepository('InstitutionBundle:Institution')->find($request->getSession()->get('institutionId'));
        
        if (!$institution) {
            throw $this->createNotFoundException('Invalid institution');
        }
        
        $newInstitutionMedicalCenter = new InstitutionMedicalCenter();
        $newInstitutionMedicalCenter->setInstitution($institution);
        $form = $this->createForm(new InstitutionMedicalCenterType(), $newInstitutionMedicalCenter);
        $form->bind($request);
        
        if ($form->isValid()) {
            $medicalCenter = $this->getDoctrine()->getRepository('MedicalProcedureBundle:MedicalCenter')->find($form->get('medical_center_id')->getData());
            if (!$medicalCenter) {
                throw new \Exception("Invalid MedicalCenter");
            }
            $newInstitutionMedicalCenter->setMedicalCenter($medicalCenter);
            
            try {
                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($newInstitutionMedicalCenter);
                $em->flush();
            } catch (\Exception $e) {
                return $this->_errorResponse($e->getMessage(), 500);
            }
        }
        else {
            if ($request->request->get('fromIndex', false)) {
                $request->getSession()->setFlash('error', 'Invalid medical center.');
                return $this->redirect($this->generateUrl('institution_medical_center_index'));   
            }
        }
    }

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
	
	private function _errorResponse($message, $code=500)
	{
	    return new Response($message, $code);
	}
}