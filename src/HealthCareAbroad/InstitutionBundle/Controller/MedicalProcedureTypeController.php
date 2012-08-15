<?php 

namespace HealthCareAbroad\InstitutionBundle\Controller;

use HealthCareAbroad\InstitutionBundle\Form\InstitutionMedicalProcedureFormType;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalProcedure;

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
        
        $institutionMedicalProcedureTypes = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionMedicalProcedureType')->findAll();
        
        $params = array(
            'institutionMedicalProcedureTypes' => $institutionMedicalProcedureTypes
        );
        return $this->render('InstitutionBundle:MedicalProcedureType:index.html.twig', $params);
    }
    
    public function addAction(Request $request)
    {
        $institution = $this->getDoctrine()->getRepository('InstitutionBundle:Institution')->find($request->getSession()->get('institutionId'));
        if (!$institution) {
            throw $this->createNotFoundException('Invalid institution');
        }
        $institutionMedicalProcedureType = new InstitutionMedicalProcedureType();
        $form = $this->createForm(new InstitutionMedicalProcedureTypeFormType(),$institutionMedicalProcedureType, array('institution' => $institution));
        
        return $this->render('InstitutionBundle:MedicalProcedureType:form.html.twig', array(
            'form' => $form->createView(),
            'institutionMedicalProcedureType' => $institutionMedicalProcedureType,
            'newListing' => true
        ));
    }
    
    public function editAction(Request $request)
    {
        $institution = $this->getDoctrine()->getRepository('InstitutionBundle:Institution')->find($request->getSession()->get('institutionId'));
        if (!$institution) {
            throw $this->createNotFoundException('Invalid institution');
        }
        
        $institutionMedicalProcedureType = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionMedicalProcedureType')->find($request->get('id', 0));
        if (!$institutionMedicalProcedureType) {
            throw $this->createNotFoundException('Invalid InstitutionMedicalProcedureType');
        }
        
        $form = $this->createForm(new InstitutionMedicalProcedureTypeFormType(),$institutionMedicalProcedureType, array('institution' => $institution));
        return $this->render('InstitutionBundle:MedicalProcedureType:form.html.twig', array(
            'form' => $form->createView(),
            'institutionMedicalProcedureType' => $institutionMedicalProcedureType,
            'newListing' => false
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
        
        $form = $this->createForm(new InstitutionMedicalProcedureTypeFormType(), $institutionMedicalProcedureType, array('institution' => $institution));
        $form->bindRequest($request);
        $isNew = $institutionMedicalProcedureType->getId() == 0;
        if ($form->isValid()){
            $institutionMedicalProcedureType = $form->getData();
            $institutionMedicalProcedureType->setInstitution($institution);
            $institutionMedicalProcedureType->setStatus(InstitutionMedicalProcedureType::STATUS_ACTIVE);
            
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($institutionMedicalProcedureType);
            $em->flush($institutionMedicalProcedureType);
            
            $request->getSession()->setFlash('success', 'Successfully saved listing.');
            
            return $this->redirect($this->generateUrl('institution_medicalProcedureType_edit', array('id' => $institutionMedicalProcedureType->getId())));
        }
        
        return $this->render('InstitutionBundle:MedicalProcedureType:form.html.twig', array(
            'form' => $form->createView(),
            'institutionMedicalProcedureType' => $institutionMedicalProcedureType,
            'newListing' => $isNew
        ));
    }
    
    public function addMedicalProcedureAction(Request $request)
    {
        $institutionMedicalProcedureType = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionMedicalProcedureType')->find($request->get('id', 0));
        if (!$institutionMedicalProcedureType) {
            throw $this->createNotFoundException('Invalid InstitutionMedicalProcedureType');
        }
        
        $form = $this->createForm(new InstitutionMedicalProcedureFormType(), new InstitutionMedicalProcedure(), array('institutionMedicalProcedureType' => $institutionMedicalProcedureType));
        $params = array(
            'id' => $institutionMedicalProcedureType->getId(),
            'procedureTypeName' => $institutionMedicalProcedureType->getMedicalProcedureType()->getName(),
            'medicalCenterName' => $institutionMedicalProcedureType->getMedicalProcedureType()->getMedicalCenter()->getName(),
            'form' => $form->createView()
        );
        return $this->render('InstitutionBundle:MedicalProcedureType:form.procedure.html.twig', $params);
        //return $this->render('InstitutionBundle:Default:index.html.twig');
    }
    
    public function saveMedicalProcedureAction(Request $request)
    {
        $institutionMedicalProcedureType = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionMedicalProcedureType')->find($request->get('id', 0));
        if (!$institutionMedicalProcedureType) {
            throw $this->createNotFoundException('Invalid InstitutionMedicalProcedureType');
        }
        
        $institutionMedicalProcedure = new InstitutionMedicalProcedure();
        $form = $this->createForm(new InstitutionMedicalProcedureFormType(), $institutionMedicalProcedure, array('institutionMedicalProcedureType' => $institutionMedicalProcedureType));
        $form->bindRequest($request);
        
        if ($form->isValid()) {

            $institutionMedicalProcedure = $form->getData();
            $institutionMedicalProcedure->setInstitutionMedicalProcedureType($institutionMedicalProcedureType);
            
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($institutionMedicalProcedure);
            $em->flush();
            $request->getSession()->setFlash('success', "Successfully added a medical procedure to {$institutionMedicalProcedureType->getMedicalProcedureType()->getName()} listing.");
            
            return $this->redirect($this->generateUrl('institution_medicalProcedureType_edit', array('id' => $institutionMedicalProcedureType->getId())));
        }
        
        $params = array(
            'id' => $institutionMedicalProcedureType->getId(),
            'procedureTypeName' => $institutionMedicalProcedureType->getMedicalProcedureType()->getName(),
            'medicalCenterName' => $institutionMedicalProcedureType->getMedicalProcedureType()->getMedicalCenter()->getName(),
            'form' => $form->createView()
        );
        
        return $this->render('InstitutionBundle:MedicalProcedureType:form.procedure.html.twig', $params);
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