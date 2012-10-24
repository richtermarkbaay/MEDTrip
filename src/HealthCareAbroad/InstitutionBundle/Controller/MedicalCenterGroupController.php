<?php
namespace HealthCareAbroad\InstitutionBundle\Controller;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter;

use HealthCareAbroad\InstitutionBundle\Form\InstitutionMedicalCenterFormType;

use Symfony\Component\HttpFoundation\Response;

use HealthCareAbroad\InstitutionBundle\Services\InstitutionMedicalCenterGroupService;

use HealthCareAbroad\InstitutionBundle\Repository\InstitutionMedicalCenterGroupRepository;

use HealthCareAbroad\InstitutionBundle\Form\InstitutionMedicalCenterGroupFormType;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenterGroup;

use Symfony\Component\HttpFoundation\Request;

/**
 * Controller for InstitutionMedicalCenterGroup.
 * 
 * @author Allejo Chris G. Velarde
 *
 */
class MedicalCenterGroupController extends InstitutionAwareController
{
    /**
     * @var InstitutionMedicalCenterGroup
     */
    private $institutionMedicalCenterGroup = null;
    
    /**
     * @var InstitutionMedicalCenterGroupRepository
     */
    private $repository;
    
    /**
     * @var InstitutionMedicalCenterGroupService
     */
    private $service;
    
    public function preExecute()
    {
        $this->repository = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionMedicalCenterGroup');
        $this->service = $this->get('services.institution_medical_center_group');
        
        if ($imcgId=$this->getRequest()->get('imcgId',0)) {
            $this->institutionMedicalCenterGroup = $this->repository->find($imcgId);
            
            // non-existent medical center group
            if (!$this->institutionMedicalCenterGroup) {
                if ($this->getRequest()->isXmlHttpRequest()) {
                    throw $this->createNotFoundException('Invalid medical center group.');
                }
                else {
                    return $this->_redirectIndexWithFlashMessage('Invalid medical center group.', 'error');
                }
            }
            
            // medical center group does not belong to this institution
            if ($this->institutionMedicalCenterGroup->getInstitution()->getId() != $this->institution->getId()) {
                return $this->_redirectIndexWithFlashMessage('Invalid medical center group.', 'error');
            }
        }
    }
    
    public function indexAction(Request $request)
    {
        //$this->institution
        
        var_dump(count($this->filteredResult)); exit;
    }
    
    /**
     * This is the first step when creating a new InstitutionMedicalCenterGroup. Add details of a InstitutionMedicalCenterGroup
     * 
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addDetailsAction(Request $request)
    {
        if (is_null($this->institutionMedicalCenterGroup)) {
            $this->institutionMedicalCenterGroup = new InstitutionMedicalCenterGroup();
            $this->institutionMedicalCenterGroup->setInstitution($this->institution);
        }
        else {
            // there is an imcgId in the Request, check if this is a draft
            if ($this->institutionMedicalCenterGroup && !$this->service->isDraft($this->institutionMedicalCenterGroup)) {
                return $this->_redirectIndexWithFlashMessage('Invalid draft medical center group', 'error');
            }
        }
        
        $form = $this->createForm(new InstitutionMedicalCenterGroupFormType(),$this->institutionMedicalCenterGroup);
        if ($request->isMethod('POST')) {
            $form->bind($request);
            
            if ($form->isValid()) {
                
                $this->institutionMedicalCenterGroup = $this->get('services.institutionMedicalCenterGroup')
                    ->saveAsDraft($form->getData());
                
                // TODO: fire event
                
                // redirect to step 2;
                return $this->redirect($this->generateUrl('institution_medicalCenterGroup_addSpecializations',array('imcgId' => $this->institutionMedicalCenterGroup->getId())));
            }
        }
        
        return $this->render('InstitutionBundle:MedicalCenterGroup:addDetails.html.twig', array('form' => $form->createView(), 'institutionMedicalCenterGroup' => $this->institutionMedicalCenterGroup));
    }
    
    /**
     * This is the second step when creating a center. This will add InstitutionMedicalCenter to the passed InstitutionMedicalCenterGroup.
     * Expected GET parameters:
     *     - imcgId institutionMedicalCenterGroupId
     * 
     * @author Allejo Chris G. Velarde
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function addSpecializationsAction(Request $request)
    {
        // should only be accessed by Draft InstitutionMedicalCenterGroup
        if (!$this->service->isDraft($this->institutionMedicalCenterGroup)) {
            
            return $this->_redirectIndexWithFlashMessage('Invalid draft medical center group', 'error');
        }
        
        $institutionMedicalCenter = new InstitutionMedicalCenter();
        $institutionMedicalCenter->setInstitutionMedicalCenterGroup($this->institutionMedicalCenterGroup);
        $form = $this->createForm(new InstitutionMedicalCenterFormType(), $institutionMedicalCenter);
        
        if ($request->isMethod('POST')) {
            $form->bind($request);
            
            if ($form->isValid()) {
                $institutionMedicalCenter = $form->getData();
                $institutionMedicalCenter->setStatus(InstitutionMedicalCenter::STATUS_ACTIVE);
                $this->get('services.institution_medical_center')->save($institutionMedicalCenter);
                
                // redirect to third step
                return $this->redirect($this->generateUrl('institution_medicalCenterGroup_addDoctors',array('imcgId' => $this->institutionMedicalCenterGroup->getId())));
            }
            
        }
        
        return $this->render('InstitutionBundle:MedicalCenterGroup:addSpecializations.html.twig', array(
            'institutionMedicalCenterGroup' => $this->institutionMedicalCenterGroup,
            'form' => $form->createView()
        ));
    }
    
    public function addDoctorsAction()
    {
        return $this->render('InstitutionBundle:MedicalCenterGroup:addDoctors.html.twig', array(
            'institutionMedicalCenterGroup' => $this->institutionMedicalCenterGroup,
            'currentDoctors' => $this->institutionMedicalCenterGroup->getDoctors()
        ));
    }
    
    public function editAction(Request $request)
    {
    
    }
    
    public function saveAction(Request $request)
    {
    
    }
    
    /**
     * Ajax request handler for loading available specializations for an institution medical center group. 
     * This is used in the dropdown data for the Specialization field in add center form.
     * Current implementation implies that we can load all active MedicalCenters, since an InstitutionMedicalCenterGroup can have one or more InstitutionMedicalCenters 
     * 
     */
    public function loadAvailableSpecializationsAction()
    {
        // load all active medical centers
        $centers = $this->get('services.medical_center')->getAllActiveMedicalCenters();
        $html = '';
        foreach ($centers as $center) {
            $html .= "<option value='{$center->getId()}'>{$center->getName()}</option>";
        }
        
        return new Response(\json_encode(array('html' => $html)),200, array('content-type' => 'application/json'));
    }
    
    /**
     * Ajax handler for loading data 
     * Expected GET parameters
     *     - imcgId instituitonMedicalCenterGroupid
     *     - medicalCenterId medicalCenterId
     */
    public function loadAvailableTreatmentsAction(Request $request)
    {
        $medicalCenter = $this->getDoctrine()->getRepository('MedicalProcedureBundle:MedicalCenter')->find($request->get('medicalCenterId', 0));
        if (!$medicalCenter) {
            throw $this->createNotFoundException("Invalid medical center");
        }
        
        // get all active TreatmentProcedures under MedicalCenter
        $treatmentProcedures = $this->get('services.treatment_procedure')->getActiveTreatmentProceduresByMedicalCenter($medicalCenter);
        $html = '';
        
        if (count($treatmentProcedures)) {
            $currentTreatment = $treatmentProcedures[0]->getTreatment();
            $html .= "<optgroup label='{$currentTreatment->getName()}'>";
            foreach ($treatmentProcedures as $each) {
            
                if ($each->getTreatment()->getId() != $currentTreatment->getId()) {
                    $currentTreatment = $each->getTreatment();
                    $html .= "</optgroup><optgroup label='{$currentTreatment->getName()}'>";
                }
                $html .= "<option value='{$each->getId()}' style='margin-left:10px;'>{$each->getName()}</option>";
            }
            $html .= "</optgroup>";
        }
        
        
        return new Response(\json_encode(array('html' => $html)),200, array('content-type' => 'application/json'));
    }
    
    /**
     * Ajax handler for searching available doctors for an InstitutionMedicalCenterGroup
     * Expected GET parameters:
     *     - imcgId institutionMedicalCenterGroupId
     *     - searchKey
     * 
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function searchAvailableDoctorAction(Request $request)
    {
        $searchKey = \trim($request->get('searchKey',''));
        $availableDoctors = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionMedicalCenterGroup')
            ->findAvailableDoctorBySearchKey($this->institutionMedicalCenterGroup, $searchKey);
        
        $output = array();
        foreach ($availableDoctors as $doctor) {
            $arr = $this->get('services.doctor.twig.extension')->doctorToArray($doctor);
            $arr['html'] = $this->renderView('InstitutionBundle:MedicalCenterGroup:doctorListItem.html.twig', array('imcgId' => $this->institutionMedicalCenterGroup->getId(),'doctor' => $doctor));
            $output[] = $arr;
        }
        
        //return $this->render('::base.ajaxDebugger.html.twig');
        return new Response(\json_encode($output),200, array('content-type' => 'application/json'));
    }
    
    /**
     * Ajax handler for adding existing doctor to an InstitutionMedicalCenterGroup
     * Expected parameters:
     *     - imcgId institutionMedicalCenterGroupId
     *     - doctorId
     * 
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addExistingDoctorAction(Request $request)
    {
        $doctor = $this->getDoctrine()->getRepository('DoctorBundle:Doctor')->find($request->get('doctorId', 0));
        if (!$doctor) {
            throw $this->createNotFoundException('Invalid doctor.');
        }
        
        try{
            $this->institutionMedicalCenterGroup->addDoctor($doctor);
            $this->service->save($this->institutionMedicalCenterGroup);
        }
        catch (\Exception $e) {
                
        }
        
        return new Response(\json_encode(array()),200, array('content-type' => 'application/json'));
    }
    
    /**
     * Ajax handler for removing a Doctor from InstitutionMedicalCenterGroup
     * Expected parameters:
     *     - imcgId
     *     - doctorId
     * 
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function removeDoctorAction(Request $request)
    {
        $doctor = $this->getDoctrine()->getRepository('DoctorBundle:Doctor')->find($request->get('doctorId', 0));
        if (!$doctor) {
            throw $this->createNotFoundException('Invalid doctor.');
        }

        try{
            $this->institutionMedicalCenterGroup->removeDoctor($doctor);
            $this->service->save($this->institutionMedicalCenterGroup);
        }
        catch (\Exception $e) {
        
        }
        
        return new Response(\json_encode(array()),200, array('content-type' => 'application/json'));
    }
    
    /**
     * Convenience function to redirect to medical center group index page with flash notice
     * 
     * @param string $flashMessage
     * @param string $type
     * @param string $redirectRoute
     */
    private function _redirectIndexWithFlashMessage($flashMessage, $type='success')
    {
        $this->getRequest()->getSession($type, $flashMessage);
        
        return $this->redirect($this->generateUrl('institution_medicalCenterGroup_index'));
    }
}