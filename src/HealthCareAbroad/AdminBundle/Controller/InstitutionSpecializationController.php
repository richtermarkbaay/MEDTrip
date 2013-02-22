<?php

namespace HealthCareAbroad\AdminBundle\Controller;

use HealthCareAbroad\TreatmentBundle\Entity\Specialization;

use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\HttpFoundation\Request;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionSpecialization;
use HealthCareAbroad\InstitutionBundle\Form\InstitutionSpecializationSelectorFormType;
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
        $treatment = $this->getDoctrine()->getRepository('TreatmentBundle:Treatment')->find($request->get('tId', 0));
        
        if (!$treatment) {
            throw $this->createNotFoundException("Invalid treatment {$request->get('tId', 0)}.");
        }
        
        $this->institutionSpecialization->removeTreatment($treatment);
        
       if ($request->isMethod('POST')) {
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($this->institutionSpecialization);
            $em->flush();
            $response = new Response("Treatment removed", 200);
        }
        
        return $response;
    }
    
    public function ajaxAddMedicalSpecializationTreatmentsAction(Request $request)
    {
        if ($request->isMethod('POST')) {
            $submittedSpecializations = $request->get(InstitutionSpecializationFormType::NAME);
            $em = $this->getDoctrine()->getEntityManager();
            $errors = array();
            $output = array('html' => '');
            foreach ($submittedSpecializations as $_isId => $_data) {
                
                if ($_isId == $this->institutionSpecialization->getSpecialization()->getId()) {
                    
                    // set passed treatments as choices
                    $default_choices = array();
                    $_treatment_choices = $this->get('services.treatment_bundle')->findTreatmentsByIds($_data['treatments']);
                    foreach ($_treatment_choices as $_t) {
                        
                        $default_choices[$_t->getId()] = $_t->getName();
                        // add the treatment
                        $this->institutionSpecialization->addTreatment($_t);
                    }
                    $form = $this->createForm(new InstitutionSpecializationFormType(), $this->institutionSpecialization, array('default_choices' => $default_choices));
                    $form->bind($_data);
                    if ($form->isValid()) {
                        try {
                            $em->persist($this->institutionSpecialization);
                            $em->flush();
                            $output['html'] = $this->renderView('AdminBundle:InstitutionTreatments:list.institutionTreatments.html.twig', array(
                                            'institutionSpecialization' => $this->institutionSpecialization,
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
            $form = $this->createForm(new InstitutionSpecializationFormType(), new InstitutionSpecialization());
            $specialization = $this->institutionSpecialization->getSpecialization();
            $availableTreatments = $this->get('services.institution_medical_center')
            ->getAvailableTreatmentsByInstitutionSpecialization($this->institutionSpecialization);
    
            $html = $this->renderView('AdminBundle:Widgets:modalEditSpecializationForm.html.twig', array(
                        'availableTreatments' => $availableTreatments,
                        'form' => $form->createView(),
                        'formName' => InstitutionSpecializationFormType::NAME,
                        'specialization' => $specialization,
                        'institutionMedicalCenter' => $this->institutionMedicalCenter,
                        'institutionSpecialization' => $this->institutionSpecialization,
                        //'currentTreatments' => $institutionSpecialization->getTreatments(),
                        'institution' => $this->institution
            ));

            $response = new Response(\json_encode(array('html' => $html)));
        }
    
        return $response;
    }
    
    /**
     * Add a new specialization to medical center
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addSpecializationAction(Request $request)
    {
        $service = $this->get('services.institution_medical_center');
    
        if ($request->isMethod('POST')) {
            
            $submittedSpecializations = $request->get(InstitutionSpecializationFormType::NAME);
            $em = $this->getDoctrine()->getEntityManager();
            $errors = array();
            if (\count($submittedSpecializations) > 0) {
                foreach ($submittedSpecializations as $specializationId => $_data) {
                    $specialization = $this->get('services.treatment_bundle')->getSpecialization($specializationId);
                    $_institutionSpecialization = new InstitutionSpecialization();
                    $_institutionSpecialization->setSpecialization($specialization);
                    $_institutionSpecialization->setInstitutionMedicalCenter($this->institutionMedicalCenter);
                    $_institutionSpecialization->setStatus(InstitutionSpecialization::STATUS_ACTIVE);
                    $_institutionSpecialization->setDescription('');
                    
                    // set passed treatments as choices
                    $default_choices = array();
                    $_treatment_choices = $this->get('services.treatment_bundle')->findTreatmentsByIds($_data['treatments']);
                    foreach ($_treatment_choices as $_t) {
                        $default_choices[$_t->getId()] = $_t->getName();
                        // add the treatment
                        $_institutionSpecialization->addTreatment($_t);
                    }
                    
                    $form = $this->createForm(new InstitutionSpecializationFormType(), $_institutionSpecialization, array('default_choices' => $default_choices));
                    $form->bind($_data);
                    if ($form->isValid()) {
                        $em->persist($_institutionSpecialization);
                        $em->flush();
                    }
                    else {
    
                    }
                }
            }
            else {
                $errors[] = 'Please provide at least one specialization.';
            }
    
            if (\count($errors) > 0) {
                $request->getSession()->setFlash('notice', '<ul><li>'.\implode('</li><li>', $errors).'</li></ul>');
            }
    
            $response = $this->redirect($this->generateUrl('admin_institution_medicalCenter_view', array('institutionId' => $this->institution->getId(), 'imcId' => $this->institutionMedicalCenter->getId())));
    
        }
        else {
            $form = $this->createForm(new InstitutionSpecializationSelectorFormType());
            $assignedSpecialization = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionSpecialization')->findByInstitutionMedicalCenter($this->institutionMedicalCenter);
            $specializations = $this->getDoctrine()->getRepository('TreatmentBundle:Specialization')->getAvailableSpecializations($assignedSpecialization);
            $specializationArr = array();
            foreach ($specializations as $e) {
                $specializationArr[] = array('value' => $e->getName(), 'id' => $e->getId());
            }
    
            $params = array(
                            'form' => $form->createView(),
                            'institution' => $this->institution,
                            'institutionMedicalCenter' => $this->institutionMedicalCenter,
                            'selectedSubMenu' => 'centers',
                            'specializationsJSON' => \json_encode($specializationArr),
            );
    
            $response = $this->render('AdminBundle:InstitutionTreatments:addSpecializations.html.twig', $params);
        }
    
        return $response;
    }
    
    
    /**
     * Add a new specialization to medical center through ajax
     *
     */
    public function ajaxAddSpecializationAction(Request $request)
    {
        $specializationId = $request->get('specializationId', 0);
        $criteria = array('status' => Specialization::STATUS_ACTIVE, 'id' => $specializationId);
    
        $params['specialization'] = $this->getDoctrine()->getRepository('TreatmentBundle:Specialization')->findOneBy($criteria);
    
        if(!$params['specialization']) {
            $result = array('error' => 'Invalid Specialization');
    
            return new Response('Invalid Specialization', 404);
        }
    
        $groupBySubSpecialization = true;
        $form = $this->createForm(new InstitutionSpecializationFormType(), new InstitutionSpecialization());
        $params['formName'] = InstitutionSpecializationFormType::NAME;
        $params['form'] = $form->createView();
        $params['subSpecializations'] = $this->get('services.treatment_bundle')->getTreatmentsBySpecializationGroupedBySubSpecialization($params['specialization']);
        $params['showCloseBtn'] = $this->getRequest()->get('showCloseBtn', true);
        $params['selectedTreatments'] = $this->getRequest()->get('selectedTreatments', array());
        $params['treatmentsListOnly'] = (bool)$this->getRequest()->get('treatmentsListOnly', 0);
    
        $html = $this->renderView('AdminBundle:InstitutionTreatments/Partials:specializationAccordion.html.twig', $params);
        return new Response(\json_encode(array('html' => $html)), 200, array('content-type' => 'application/json'));
    }
}