<?php

namespace HealthCareAbroad\AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\HttpFoundation\Request;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionSpecialization;
use HealthCareAbroad\InstitutionBundle\Form\InstitutionSpecializationFormType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class InstitutionSpecializationController extends Controller
{
    /**
     * @var Institution
     */
    private $institution;
    
    /**
     * @var InstitutionMedicalCenter
     */
    private $institutionMedicalCenter;
    
    /**
     * @var InstitutionSpecialization
     */
    private $institutionSpecialization;
    
    /**
     * @var Request
     */
    private $request;
    
    public function preExecute()
    {
        $this->request = $this->getRequest();
        $this->institution = $this->get('services.institution.factory')->findById($this->request->get('institutionId', 0));
        // Check Institution
        if(!$this->institution) {
            throw $this->createNotFoundException('Invalid Institution');
        }
    
        // check InstitutionMedicalCenter
        if ($institutionMedicalCenterId = $this->request->get('imcId', 0)) {
            $this->institutionMedicalCenter = $this->get('services.institution_medical_center')->findById($institutionMedicalCenterId);
            if (!$this->institutionMedicalCenter) {
                throw $this->createNotFoundException('Invalid institution medical center');
            }
        }
        
        // check  Institution specialization
        if ($isId = $this->request->get('isId', 0)) {
            $this->institutionSpecialization = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionSpecialization')
                ->find($isId);
            if (!$this->institutionSpecialization) {
                throw $this->createNotFoundException("Invalid insitution specialization {$isId}");
            }
        }
        
    }
    
    /**
     * Remove a Treatmnent from an institution specialization
     * Expected parameters
     *
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function ajaxRemoveSpecializationTreatmentAction(Request $request)
    {
        $institutionSpecialization = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionSpecialization')->find($request->get('isId', 0));
        $treatment = $this->getDoctrine()->getRepository('TreatmentBundle:Treatment')->find($request->get('tId', 0));
        
        if (!$institutionSpecialization) {
            throw $this->createNotFoundException("Invalid institution specialization {$institutionSpecialization->getId()}.");
        }
        if (!$treatment) {
            throw $this->createNotFoundException("Invalid treatment {$treatment->getId()}.");
        }
        
        $institutionSpecialization->removeTreatment($treatment);
        
        try {
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($institutionSpecialization);
            $em->flush();
            $response = new Response("Treatment removed", 200);
        }
        catch (\Exception $e) {
            $response = new Response($e->getMessage(), 500);
        }
        
        return $response;
    }
    
    public function ajaxAddMedicalSpecializationTreatmentsAction(Request $request)
    {
        $institutionSpecialization = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionSpecialization')->find($request->get('isId'));
        if (!$institutionSpecialization ) {
            throw $this->createNotFoundException('Invalid institution specialization');
        }
    
        $form = $this->createForm(new InstitutionSpecializationFormType(), new InstitutionSpecialization(), array('em' => $this->getDoctrine()->getEntityManager()));
        if ($request->isMethod('POST')) {
            $submittedSpecializations = $request->get(InstitutionSpecializationFormType::NAME);
            $em = $this->getDoctrine()->getEntityManager();
            $errors = array();
            $output = array('html' => '');
            foreach ($submittedSpecializations as $_isId => $_data) {
                if ($_isId == $institutionSpecialization->getSpecialization()->getId()) {
    
                    $form = $this->createForm(new InstitutionSpecializationFormType(), $institutionSpecialization, array('em' => $em));
                    $form->bind($_data);
                    if ($form->isValid()) {
                        try {
                            $em->persist($form->getData());
                            $em->flush();
                            $output['html'] = $this->renderView('AdminBundle:InstitutionTreatments:list.institutionTreatments.html.twig', array(
                                            'institutionSpecialization' => $institutionSpecialization,
                                            'institutionMedicalCenter' => $this->institutionMedicalCenter,
                                            'institution' => $this->institution
                            ));
                        }catch (\Exception $e) {
                            $errors[] = $e->getMessage();
                        }
                    }
                    else {
                        $errors[] = 'Failed form validation';
                    }
                }
            }
    
            if (\count($errors) > 0) {
                $response = new Response('Errors: '.implode('\n',$errors), 400);
            }
            else {
                $response = new Response(\json_encode($output), 200, array('content-type' => 'application/json'));
            }
        }
        else {
            $specialization = $institutionSpecialization->getSpecialization();
            $availableTreatments = $this->get('services.institution_medical_center')
            ->getAvailableTreatmentsByInstitutionSpecialization($institutionSpecialization);
    
            try {
                $html = $this->renderView('AdminBundle:Widgets:modalEditSpecializationForm.html.twig', array(
                            'availableTreatments' => $availableTreatments,
                            'form' => $form->createView(),
                            'formName' => InstitutionSpecializationFormType::NAME,
                            'specialization' => $specialization,
                            'institutionMedicalCenter' => $this->institutionMedicalCenter,
                            'institutionSpecialization' => $institutionSpecialization,
                            'currentTreatments' => $institutionSpecialization->getTreatments(),
                            'institution' => $this->institution
                ));
    
                $response = new Response(\json_encode(array('html' => $html)));
            }
            catch (\Exception $e) {
                $response = new Response($e->getMessage(), 500);
            }
    
        }
    
        return $response;
    }
}