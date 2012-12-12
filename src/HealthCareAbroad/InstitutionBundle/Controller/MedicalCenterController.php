<?php
namespace HealthCareAbroad\InstitutionBundle\Controller;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionSpecialization;

use HealthCareAbroad\InstitutionBundle\Form\InstitutionAffiliationFormType;

use HealthCareAbroad\HelperBundle\Form\InstitutionSpecializationFormType;

use Symfony\Component\HttpFoundation\Response;

use HealthCareAbroad\InstitutionBundle\Services\InstitutionMedicalCenterService;

use HealthCareAbroad\InstitutionBundle\Repository\InstitutionMedicalCenterRepository;

use HealthCareAbroad\InstitutionBundle\Form\InstitutionMedicalCenterFormType;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter;

use Symfony\Component\HttpFoundation\Request;

/**
 * Controller for InstitutionMedicalCenter.
 * 
 * @author Allejo Chris G. Velarde
 *
 */
class MedicalCenterController extends InstitutionAwareController
{
    /**
     * @var InstitutionMedicalCenter
     */
    private $institutionMedicalCenter = null;
    
    /**
     * @var InstitutionMedicalCenterRepository
     */
    private $repository;
    
    /**
     * @var InstitutionMedicalCenterService
     */
    private $service;
    
    public function preExecute()
    {
        $this->repository = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionMedicalCenter');
        $this->service = $this->get('services.institution_medical_center');

                
        if ($imcId=$this->getRequest()->get('imcId',0)) {
            $this->institutionMedicalCenter = $this->repository->find($imcId);
            
            // non-existent medical center group
            if (!$this->institutionMedicalCenter) {
                if ($this->getRequest()->isXmlHttpRequest()) {
                    throw $this->createNotFoundException('Invalid medical center.');
                }
                else {
                    return $this->_redirectIndexWithFlashMessage('Invalid medical center.', 'error');
                }
            }

            // medical center group does not belong to this institution
            if ($this->institutionMedicalCenter->getInstitution()->getId() != $this->institution->getId()) {
                return $this->_redirectIndexWithFlashMessage('Invalid medical center.', 'error');
            }
        }
        

    }
    
    public function indexAction(Request $request)
    {

//         var_dump(count($this->filteredResult)); exit;
        return $this->render('InstitutionBundle:MedicalCenter:index.html.twig',array(
                        'institution' => $this->institution,
                        'medicalCenters' => $this->filteredResult));
    }
    
    /**
     * This is the first step when creating a new InstitutionMedicalCenter. Add details of a InstitutionMedicalCenter
     * 
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addDetailsAction(Request $request)
    {
        if (is_null($this->institutionMedicalCenter)) {
            $this->institutionMedicalCenter = new InstitutionMedicalCenter();
            $this->institutionMedicalCenter->setInstitution($this->institution);
        }
        else {
            // there is an imcId in the Request, check if this is a draft
            if ($this->institutionMedicalCenter && !$this->service->isDraft($this->institutionMedicalCenter)) {
                return $this->_redirectIndexWithFlashMessage('Invalid draft medical center', 'error');
            }
        }
        
        $form = $this->createForm(new InstitutionMedicalCenterFormType(),$this->institutionMedicalCenter);
        if ($request->isMethod('POST')) {
            $form->bind($request);
            
            if ($form->isValid()) {
                
                $this->institutionMedicalCenter = $this->get('services.institutionMedicalCenter')
                    ->saveAsDraft($form->getData());
                
                // TODO: fire event
                
                // redirect to step 2;
                return $this->redirect($this->generateUrl('institution_medicalCenter_addSpecializations',array('imcId' => $this->institutionMedicalCenter->getId())));
            }
        }
        
        return $this->render('InstitutionBundle:MedicalCenter:addDetails.html.twig', array('form' => $form->createView(), 'institutionMedicalCenter' => $this->institutionMedicalCenter));
    }
    
    /**
     * This is the second step when creating a center. This will add InstitutionMedicalCenter to the passed InstitutionMedicalCenter.
     * Expected GET parameters:
     *     - imcId institutionMedicalCenterId
     * 
     * @author Allejo Chris G. Velarde
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function addSpecializationsAction(Request $request)
    {
        // should only be accessed by Draft InstitutionMedicalCenter
        if (!$this->service->isDraft($this->institutionMedicalCenter)) {
            
            return $this->_redirectIndexWithFlashMessage('Invalid draft medical center', 'error');
        }
        
        $institutionSpecialization = new InstitutionSpecialization();
        $institutionSpecialization->setInstitutionMedicalCenter($this->institutionMedicalCenter);
        $form = $this->createForm(new InstitutionSpecializationFormType(), $institutionSpecialization);
        
        if ($request->isMethod('POST')) {
            $form->bind($request);
            
            if ($form->isValid()) {
                $institutionSpecialization = $form->getData();
                $institutionSpecialization->setStatus(InstitutionSpecialization::STATUS_ACTIVE);
                $this->get('services.institution_specialization')->save($institutionSpecialization);

                // redirect to third step
                $params = array('imcId' => $institutionSpecialization->getId());
                return $this->redirect($this->generateUrl('institution_medicalCenter_addTreatments',$params));
            }
            
        }
        
        return $this->render('InstitutionBundle:MedicalCenter:addSpecializations.html.twig', array(
            'institutionMedicalCenter' => $this->institutionMedicalCenter,
            'form' => $form->createView()
        ));
    }
    
    public function addTreatmentsAction()
    {
        $institutionSpecializations = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionSpecialization')->getByInstitutionMedicalCenter($this->institutionMedicalCenter);

        foreach($institutionSpecializations as $each) {
            var_dump($each); exit;
//             $treatments = $each->getTreatments();
//              foreach($treatments as $treatment) {
//                  echo $treatment->getName() . ', ';                
//              }
             echo '<br/>';
        }
//exit;
       //$treatments = $this->get('services.treatment_bundle')->getSpecializationTreatments($institutionSpecialization->getSpecialization());

        $params = array('institutionMedicalCenter' => $this->institutionMedicalCenter);
        return $this->render('InstitutionBundle:MedicalCenter:addTreatments.html.twig', $params);
    }
    
    public function addDoctorsAction()
    {
        return $this->render('InstitutionBundle:MedicalCenter:addDoctors.html.twig', array(
            'institutionMedicalCenter' => $this->institutionMedicalCenter,
            'currentDoctors' => $this->institutionMedicalCenter->getDoctors()
        ));
    }
    
    public function editAction(Request $request)
    {
    
    }
    
    public function viewAction(Request $request)
    {
    
    }
    
    public function saveAction(Request $request)
    {
    
    }
    
    /**
     * Ajax request handler for loading available specializations for an institution medical center group. 
     * This is used in the dropdown data for the Specialization field in add center form.
     * Current implementation implies that we can load all active Specializations, since an InstitutionMedicalCenter can have one or more InstitutionSpecializations 
     * 
     */
    public function loadAvailableSpecializationsAction()
    {
        // load all active medical centers
        $specializations = $this->get('services.specialization')->getAllActiveSpecializations();
        $html = '';
        foreach ($specializations as $each) {
            $html .= "<option value='{$each->getId()}'>{$each->getName()}</option>";
        }
        
        return new Response(\json_encode(array('html' => $html)),200, array('content-type' => 'application/json'));
    }
    
    /**
     * Ajax handler for loading data 
     * Expected GET parameters
     *     - imcId instituitonMedicalCenterid
     *     - specializationId specializationId
     */
    public function loadAvailableTreatmentsAction(Request $request)
    {
        $specialization = $this->getDoctrine()->getRepository('TreatmentBundle:Specialization')->find($request->get('specializationId', 0));
        if (!$specialization) {
            throw $this->createNotFoundException("Invalid specialization");
        }
        
        // get all active Treatments under Specialization
        $treatments = $this->get('services.treatment')->getActiveTreatmentsBySpecialization($specialization);
        $html = '';
        
        if (count($treatments)) {
            $currentSubSpecialization = $treatments[0]->getSubSpecialization();
            $html .= "<optgroup label='{$currentSubSpecialization->getName()}'>";
            foreach ($treatments as $each) {
            
                if ($each->getTreatment()->getId() != $currentSubSpecialization->getId()) {
                    $currentSubSpecialization = $each->getSubSpecialization();
                    $html .= "</optgroup><optgroup label='{$currentSubSpecialization->getName()}'>";
                }
                $html .= "<option value='{$each->getId()}' style='margin-left:10px;'>{$each->getName()}</option>";
            }
            $html .= "</optgroup>";
        }
        
        
        return new Response(\json_encode(array('html' => $html)),200, array('content-type' => 'application/json'));
    }
    
    /**
     * Ajax handler for searching available doctors for an InstitutionMedicalCenter
     * Expected GET parameters:
     *     - imcId institutionMedicalCenterId
     *     - searchKey
     * 
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function searchAvailableDoctorAction(Request $request)
    {
        $searchKey = \trim($request->get('searchKey',''));
        $availableDoctors = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionMedicalCenter')
            ->findAvailableDoctorBySearchKey($this->institutionMedicalCenter, $searchKey);
        
        $output = array();
        foreach ($availableDoctors as $doctor) {
            $arr = $this->get('services.doctor.twig.extension')->doctorToArray($doctor);
            $arr['html'] = $this->renderView('InstitutionBundle:MedicalCenter:doctorListItem.html.twig', array('imcId' => $this->institutionMedicalCenter->getId(),'doctor' => $doctor));
            $output[] = $arr;
        }
        
        //return $this->render('::base.ajaxDebugger.html.twig');
        return new Response(\json_encode($output),200, array('content-type' => 'application/json'));
    }
    
    /**
     * Ajax handler for adding existing doctor to an InstitutionMedicalCenter
     * Expected parameters:
     *     - imcId institutionMedicalCenterId
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
            $this->institutionMedicalCenter->addDoctor($doctor);
            $this->service->save($this->institutionMedicalCenter);
        }
        catch (\Exception $e) {
                
        }
        
        return new Response(\json_encode(array()),200, array('content-type' => 'application/json'));
    }
    
    /**
     * Ajax handler for removing a Doctor from InstitutionMedicalCenter
     * Expected parameters:
     *     - imcId
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
            $this->institutionMedicalCenter->removeDoctor($doctor);
            $this->service->save($this->institutionMedicalCenter);
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
        
        return $this->redirect($this->generateUrl('institution_medicalCenter_index'));
    }
    
    /**
     * @author Chaztine Blance
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * Adding of Insitution Affiliations
     */
    public function addAffiliationsAction(Request $request)
    {
	    	$form = $this->createForm(new InstitutionAffiliationFormType(),$this->institutionMedicalCenter);
	    
	    	if ($request->isMethod('POST')) {
	    	
	    		$form->bind($request);
	    		
	    		if ($form->isValid()) {
	    
	    			$this->institutionMedicalCenter = $this->get('services.institutionMedicalCenter')
	    			->saveAsDraft($form->getData());
	    			$request->getSession()->setFlash('success', 'Affiliations has been saved!');
	    			return $this->redirect($this->generateUrl('institution_medicalCenter_addDetails',array('imcId' => $this->institutionMedicalCenter->getId())));
	    		}
	    	}
	    	
	    	return $this->render('InstitutionBundle:MedicalCenter:addAffiliation.html.twig', array(
	    					'form' => $form->createView(), 
	    					'institutionMedicalCenter' => $this->institutionMedicalCenter,
	    					'formAction' => $this->generateUrl('institution_medicalCenter_addAffiliations',
	    									array('imcId:' => $this->institutionMedicalCenter->getId())),
	    					'newObject' => true
	    					));
    }

}