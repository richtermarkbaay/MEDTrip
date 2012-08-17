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
        
        
        return $this->render('InstitutionBundle:MedicalCenter:index.html.twig', array(
            'institutionMedicalCenters' => $institutionMedicalCenters,
        ));
    }
    
    public function editAction(Request $request)
    {
        $institutionId = $request->getSession()->get('institutionId');
        $medicalCenterId = $request->get('medicalCenterId', 0);
        $institutionMedicalCenter = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionMedicalCenter')->find(array('institutionId' => $institutionId, 'medicalCenterId' => $medicalCenterId));
        
        if (!$institutionMedicalCenter) {
            throw $this->createNotFoundException("Invalid institution medical center.");
        }
        $form = $this->createForm(new InstitutionMedicalCenterType(), $institutionMedicalCenter, array('institution' => $institutionMedicalCenter->getInstitution(), 'medicalCenterId' => $medicalCenterId));
        
        return $this->render('InstitutionBundle:MedicalCenter:form.html.twig', array(
            'form' => $form->createView(),
            'medicalCenter' => $institutionMedicalCenter->getMedicalCenter(),
            'isNew' => false
        ));
    }
    
    public function addAction(Request $request)
    {
        $institution = $this->getDoctrine()->getRepository('InstitutionBundle:Institution')->find($request->getSession()->get('institutionId'));
        if (!$institution) {
            throw $this->createNotFoundException('Invalid institution');
        }
        
        $institutionMedicalCenter = new InstitutionMedicalCenter();
        $form = $this->createForm(new InstitutionMedicalCenterType(), $institutionMedicalCenter, array('institution' => $institution));
        
        if ($request->isXmlHttpRequest()) {
            return $this->render('InstitutionBundle:MedicalCenter:modalForm.html.twig', array(
                'form' => $form->createView()
            ));
        }
        
        return $this->render('InstitutionBundle:MedicalCenter:form.html.twig', array(
            'form' => $form->createView(),
            'medicalCenter' => null,
            'isNew' => true
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
        
        if ($medicalCenterId= $request->get('medicalCenterId', 0)) {
            $institutionMedicalCenter = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionMedicalCenter')->find(array('institutionId' => $institution->getId(), 'medicalCenterId' => $medicalCenterId));
            if (!$institutionMedicalCenter) {
                throw $this->createNotFoundException("Invalid institution medical center.");
            }
            $isNew = false;
        }
        else {
            $institutionMedicalCenter = new InstitutionMedicalCenter();
            $isNew = true;
        }
        $form = $this->createForm(new InstitutionMedicalCenterType(), $institutionMedicalCenter, array('institution' => $institution));
        $form->bind($request);
        
        if ($form->isValid()) {
            
            try {
                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($institutionMedicalCenter);
                $em->flush();
            } catch (\Exception $e) {
                return $this->_errorResponse($e->getMessage(), 500);
            }
            
            $request->getSession()->setFlash('success', "Successfully added {$institutionMedicalCenter->getMedicalCenter()->getName()} medical center.");
            return $this->redirect($this->generateUrl('institution_medicalCenter_index'));
        }
        else {
            return $this->render('InstitutionBundle:MedicalCenter:form.html.twig', array(
                'form' => $form->createView(),
                'medicalCenter' => $isNew ? null: $institutionMedicalCenter->getMedicalCenter(),
                'isNew' => $isNew
            ));
        }
    }
    
    public function deleteAction()
    {
        $institution = $this->getDoctrine()->getRepository('InstitutionBundle:Institution')->find($request->getSession()->get('institutionId'));
        if (!$institution) {
            throw $this->createNotFoundException('Invalid institution');
        }
        
        
    }

	function loadProcedureTypesAction(Request $request)
	{
	    $institution = $this->getDoctrine()->getRepository('InstitutionBundle:Institution')->find($request->getSession()->get('institutionId'));
	    if (!$institution) {
	        throw $this->createNotFoundException('Invalid institution');
	    }
	    
		$data = array();
		$em = $this->getDoctrine()->getEntityManager();
		$repo = $em->getRepository('InstitutionBundle:InstitutionMedicalCenter');
		$institutionMedicalCenter = $repo->findOneBy(array('institutionId' => $institution->getId(), 'medicalCenterId' => $request->get('medical_center_id')));
		
		if (!$institutionMedicalCenter) {
		    throw $this->createNotFoundException('No InstitutionMedicalCenter found.');
		}
		
		$procedureTypes =  $repo->getAvailableMedicalProcedureTypes($institutionMedicalCenter);
		foreach($procedureTypes as $each) {
		    $data[] = array('id' => $each->getId(), 'name' => $each->getName());
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