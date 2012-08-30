<?php 

namespace HealthCareAbroad\InstitutionBundle\Controller;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter;

use HealthCareAbroad\InstitutionBundle\Form\InstitutionMedicalCenterType;

use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Response;

class MedicalCenterController extends InstitutionAwareController
{
    
    public function indexAction(Request $request)
    {
        $institutionRepository = $this->getDoctrine()->getRepository('InstitutionBundle:Institution'); 
        $institutionMedicalCenters = $institutionRepository->getActiveInstitutionMedicalCenters($this->institution);
        
        
        return $this->render('InstitutionBundle:MedicalCenter:index.html.twig', array(
            'institutionMedicalCenters' => $institutionMedicalCenters,
        ));
    }
    
    public function editAction(Request $request)
    {
        $institutionMedicalCenter = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionMedicalCenter')->find($request->get('imcid', 0));
        
        if (!$institutionMedicalCenter) {
            throw $this->createNotFoundException("Invalid institution medical center.");
        }
        $form = $this->createForm(new InstitutionMedicalCenterType(), $institutionMedicalCenter);
        
        return $this->render('InstitutionBundle:MedicalCenter:form.html.twig', array(
            'form' => $form->createView(),
            'isNew' => false,
            'institutionMedicalCenter' => $institutionMedicalCenter
        ));
    }
    
    public function addAction(Request $request)
    {
        $institution = $this->getDoctrine()->getRepository('InstitutionBundle:Institution')->find($request->getSession()->get('institutionId'));
        if (!$institution) {
            throw $this->createNotFoundException('Invalid institution');
        }
        
        $institutionMedicalCenter = new InstitutionMedicalCenter();
        $institutionMedicalCenter->setInstitution($institution);
        $form = $this->createForm(new InstitutionMedicalCenterType(), $institutionMedicalCenter);
        
        return $this->render('InstitutionBundle:MedicalCenter:form.html.twig', array(
            'form' => $form->createView(),
            'isNew' => true,
            'institutionMedicalCenter' => $institutionMedicalCenter
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
        
        if ($imcid= $request->get('imcid', 0)) {
            $institutionMedicalCenter = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionMedicalCenter')->find($imcid);
            if (!$institutionMedicalCenter) {
                throw $this->createNotFoundException("Invalid institution medical center.");
            }
        }
        else {
            $institutionMedicalCenter = new InstitutionMedicalCenter();
            $institutionMedicalCenter->setInstitution($institution);
        }
        $isNew = $institutionMedicalCenter->getId() == 0;
        $form = $this->createForm(new InstitutionMedicalCenterType(), $institutionMedicalCenter);
        $form->bind($request);
        
        if ($form->isValid()) {
            
            try {
                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($institutionMedicalCenter);
                $em->flush();
            } catch (\Exception $e) {
                return $this->_errorResponse($e->getMessage(), 500);
            }
            
            $request->getSession()->setFlash('success', "Successfully ".($isNew?'added':'updated')." {$institutionMedicalCenter->getMedicalCenter()->getName()} medical center.");
            return $this->redirect($this->generateUrl('institution_medicalCenter_index'));
        }
        else {
            return $this->render('InstitutionBundle:MedicalCenter:form.html.twig', array(
                'form' => $form->createView(),
                'isNew' => $isNew,
                'institutionMedicalCenter' => $institutionMedicalCenter
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

    /**
     * method no longer needed
     * TODO: remove entirely
	function loadProcedureTypesAction(Request $request)
	{
		$institutionId = $request->get('id', $request->getSession()->get('institutionId')); 
	    $institution = $this->getDoctrine()->getRepository('InstitutionBundle:Institution')->find($institutionId);
	    if (!$institution) {
	        throw $this->createNotFoundException('Invalid institution');
	    }
	    
		$data = array();
		$em = $this->getDoctrine()->getEntityManager();
		$repo = $em->getRepository('InstitutionBundle:InstitutionMedicalCenter');
		$institutionMedicalCenter = $repo->findOneBy(array('institution' => $institution->getId(), 'medicalCenter' => $request->get('medical_center_id')));
		
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
	}**/
	
	private function _errorResponse($message, $code=500)
	{
	    return new Response($message, $code);
	}
}